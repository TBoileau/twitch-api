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

final class GetTwitchCliCommand extends Command
{
    private const TWITCH_CLI_VERSION = '1.1.21';
    private const TWITCH_CLI_DEFAULT_DISTRIBUTION = 'Linux_x86_64';
    private const TWITCH_CLI_DISTRIBUTIONS = [
        'Linux_arm64' => '.tar.gz',
        'Linux_x86_64' => '.tar.gz',
        'Darwin_arm64' => '.tar.gz',
        'Darwin_x86_64' => '.tar.gz',
        'Windows_i686' => '.zip',
        'Windows_x86_64' => '.zip'
    ];

    protected static $defaultName = 'twitch:cli';

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

        $filename = sprintf('%s%s', $baseName, self::TWITCH_CLI_DISTRIBUTIONS[$distribution]);

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
            $output->writeln('<error>Download failed !</error>');
            return Command::FAILURE;
        }

        $fileHandler = fopen(sprintf('%s/%s', $this->twitchCliDir, $filename), 'w');

        foreach ($this->httpClient->stream($response) as $chunk) {
            fwrite($fileHandler, $chunk->getContent());
        }

        $progressBar->finish();

        $phar = new PharData(sprintf('%s/%s', $this->twitchCliDir, $filename));

        $phar->extractTo($this->twitchCliDir, null, true);

        $filesystem = new Filesystem();

        $filesystem->remove(sprintf('%s/twitch', $this->twitchCliDir));

        $filesystem->remove(sprintf('%s/%s', $this->twitchCliDir, $filename));

        $filesystem->rename(
            sprintf('%s/%s/twitch', $this->twitchCliDir, $baseName),
            sprintf('%s/twitch', $this->twitchCliDir)
        );

        $filesystem->remove(sprintf('%s/%s', $this->twitchCliDir, $baseName));

        $output->writeln('');

        $output->writeln('<comment>Twitch Cli downloaded !</comment>');

        $process = new Process([sprintf('%s/twitch', $this->twitchCliDir)]);
        $process->run();

        if (!$process->isSuccessful()) {
            $filesystem->remove(sprintf('%s/twitch', $this->twitchCliDir));
            $output->writeln('<error>Twitch Cli is not working !</error>');
            return Command::FAILURE;
        }

        $output->writeln('<info>Twitch Cli is now functional !</info>');

        return Command::SUCCESS;
    }
}
