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

final class ServeTwitchMockServerCommand extends Command
{
    protected static $defaultName = 'twitch:serve';

    public function __construct(private readonly string $twitchCliPath, private readonly int $twitchMockServerPort)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Serve Twitch Mock Server');

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $process = new Process([$this->twitchCliPath, 'mock-api', 'start', '-p', $this->twitchMockServerPort]);

        $process->start();

        $process->wait(function ($type, $buffer) use ($output) {
            $output->write($buffer);
        });

        return Command::SUCCESS;
    }
}
