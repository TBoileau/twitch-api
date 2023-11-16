<?php

declare(strict_types=1);

namespace TBoileau\TwitchApi\PHPUnit;

use PHPUnit\Event\Event;
use PHPUnit\Event\TestRunner\Finished;
use PHPUnit\Event\TestRunner\FinishedSubscriber;
use PHPUnit\Event\TestRunner\Started;
use PHPUnit\Event\TestRunner\StartedSubscriber;
use PHPUnit\Runner\Extension\Extension;
use PHPUnit\Runner\Extension\Facade;
use PHPUnit\Runner\Extension\ParameterCollection;
use PHPUnit\TextUI\Configuration\Configuration;
use Symfony\Component\Process\Process;

final class PHPUnitExtension implements Extension
{
    private Process $process;
    public function __construct()
    {
        $this->process = new Process(['php', 'bin/console', 'twitch:serve', '-p', $_ENV['TWITCH_MOCK_SERVER_PORT']]);
    }

    public function bootstrap(Configuration $configuration, Facade $facade, ParameterCollection $parameters): void
    {
        $facade->registerSubscriber(new class($this->process) implements StartedSubscriber {
            public function __construct(private Process $process)
            {
            }

            /**
             * @param Started $event
             */
            public function notify(Event $event): void
            {
                $this->process->start();
            }
        });

        $facade->registerSubscriber(new class($this->process) implements FinishedSubscriber {
            public function __construct(private Process $process)
            {
            }

            /**
             * @param Finished $event
             */
            public function notify(Event $event): void
            {
                $this->process->stop();
            }
        });
    }
}
