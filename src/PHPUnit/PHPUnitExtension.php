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
    private ?int $pid = null;

    public function bootstrap(Configuration $configuration, Facade $facade, ParameterCollection $parameters): void
    {
        $facade->registerSubscriber(new class($this->pid) implements StartedSubscriber {
            public function __construct(private ?int &$pid)
            {
            }

            /**
             * @param Started $event
             */
            public function notify(Event $event): void
            {
                $process = new Process(['php', 'bin/console', 'twitch:serve']);

                $this->pid = $process->getPid();

                $process->start();
            }
        });

        $facade->registerSubscriber(new class($this->pid) implements FinishedSubscriber {
            public function __construct(private ?int $pid)
            {
            }

            /**
             * @param Finished $event
             */
            public function notify(Event $event): void
            {
                $process = new Process(['kill', '-9', $this->pid]);

                $process->run();
            }
        });
    }
}
