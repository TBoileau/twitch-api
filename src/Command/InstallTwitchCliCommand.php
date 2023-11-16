<?php

declare(strict_types=1);

namespace TBoileau\TwitchApi\Command;

use PharData;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class InstallTwitchCliCommand extends Command
{
    public const TWITCH_CLI_VERSION = '1.1.21';
    public const TWITCH_CLI_DEFAULT_DISTRIBUTION = 'Linux_x86_64';
    public const TWITCH_CLI_DISTRIBUTIONS = [
        'Linux_arm64' => ['ext' => '.tar.gz', 'filename' => 'twitch'],
        'Linux_x86_64' => ['ext' => '.tar.gz', 'filename' => 'twitch'],
        'Darwin_arm64' => ['ext' => '.tar.gz', 'filename' => 'twitch'],
        'Darwin_x86_64' => ['ext' => '.tar.gz', 'filename' => 'twitch'],
        'Windows_i386' => ['ext' => '.zip', 'filename' => 'twitch.exe'],
        'Windows_x86_64' => ['ext' => '.zip', 'filename' => 'twitch.exe'],
    ];

    protected static $defaultName = 'twitch:install';

    public function __construct(private readonly HttpClientInterface $httpClient, private readonly string $twitchCliDir)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Get Twitch CLI')
            ->addOption(
                'release',
                'r',
                InputOption::VALUE_OPTIONAL,
                'Twitch CLI release',
                self::TWITCH_CLI_VERSION
            )
            ->addOption(
                'distribution',
                'd',
                InputOption::VALUE_OPTIONAL,
                'Twitch CLI distribution',
                self::TWITCH_CLI_DEFAULT_DISTRIBUTION
            );

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $release */
        $release = $input->getOption('release');

        if (!preg_match('/^\d+.\d+.\d+$/', $release)) {
            $output->writeln('<error>Version is valid and must be in the format X.X.X !</error>');
            return Command::FAILURE;
        }

        /** @var string $distribution */
        $distribution = $input->getOption('distribution');

        if (!isset(self::TWITCH_CLI_DISTRIBUTIONS[$distribution])) {
            $output->writeln('<error>Distribution is not valid !</error>');
            return Command::FAILURE;
        }

        $baseName = sprintf('twitch-cli_%s_%s', $release, $distribution);
        $filename = sprintf('%s%s', $baseName, self::TWITCH_CLI_DISTRIBUTIONS[$distribution]['ext']);
        $executable = self::TWITCH_CLI_DISTRIBUTIONS[$distribution]['filename'];
        $twitchCliPath = sprintf('%s/%s', $this->twitchCliDir, $executable);

        try {
            $tempFilename = $this->download($release, $filename, $output);

            $output->writeln('');
            $output->writeln('<info>Twitch Cli downloaded !</info>');
            $output->writeln('<comment>Extraction...</comment>');

            $this->extract($tempFilename, $baseName, $executable, $twitchCliPath);

            $output->writeln('');
            $output->writeln('<info>Twitch Cli extracted !</info>');
            $output->writeln('<comment>Generating fixtures...</comment>');

            ['clientId' => $clientId, 'secret' => $secret] = $this->generateFixtures($executable);

            $output->writeln('');
            $output->writeln(sprintf('<comment>Your fake Twitch Client ID : %s</comment>', $clientId));
            $output->writeln(sprintf('<comment>Your fake Twitch Client Secret : %s</comment>', $secret));
            $output->writeln('<info>You can now start the Mock server :</info>');
            $output->writeln('    <comment>php bin/console twitch:serve</comment>');
        } catch (\Exception $e) {
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function download(string $release, string $filename, OutputInterface $output): string
    {
        $url = sprintf(
            'https://github.com/twitchdev/twitch-cli/releases/download/v%s/%s',
            $release,
            $filename
        );

        $progressBar = new ProgressBar($output);

        $output->writeln('<comment>Download Twitch Cli</comment>');

        $response = $this->httpClient->request('GET', $url, [
            'on_progress' => function (int $dlNow, int $dlSize) use ($output, $progressBar): void {
                $progressBar->setMaxSteps($dlSize);
                $progressBar->advance($dlNow);
            },
        ]);

        if (200 !== $response->getStatusCode()) {
            throw new \HttpResponseException('Download failed !');
        }

        $tempFilename = sprintf('%s/%s', sys_get_temp_dir(), $filename);

        $fileHandler = fopen($tempFilename, 'w');

        foreach ($this->httpClient->stream($response) as $chunk) {
            fwrite($fileHandler, $chunk->getContent());
        }

        $progressBar->finish();

        return $tempFilename;
    }

    private function extract(string $tempFilename, string $baseName, string $executable, string $twitchCliPath): void
    {
        $filesystem = new Filesystem();

        if ($filesystem->exists($this->twitchCliDir)) {
            $filesystem->remove($this->twitchCliDir);
        }

        $phar = new PharData($tempFilename);

        $phar->extractTo($this->twitchCliDir, sprintf('%s/%s', $baseName, $executable), true);

        $filesystem->rename(
            sprintf('%s/%s/%s', $this->twitchCliDir, $baseName, $executable),
            $twitchCliPath,
            true
        );

        $filesystem->remove(sprintf('%s/%s', $twitchCliPath, $baseName));
    }

    /**
     * @return array{clientId: string, secret: string}
     */
    private function generateFixtures(string $executable): array
    {
        $process = new Process([sprintf('./%s', $executable), 'mock-api', 'generate'], $this->twitchCliDir);
        $process->start();

        $clientId = null;
        $secret = null;

        $process->wait(function ($type, $buffer) use (&$clientId, &$secret) {
            if (preg_match('/Client-ID: (?<clientId>[0-9a-zA-Z]+)/', $buffer, $matches)) {
                $clientId = $matches['clientId'];
            }

            if (preg_match('/Secret: (?<secret>[0-9a-zA-Z]+)/', $buffer, $matches)) {
                $secret = $matches['secret'];
            }
        });

        if (!$process->isSuccessful() || null === $clientId || null === $secret) {
            throw new \RuntimeException('Twitch Cli is not working !');
        }

        return ['clientId' => $clientId, 'secret' => $secret];
    }
}
