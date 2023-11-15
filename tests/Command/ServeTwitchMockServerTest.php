<?php

declare(strict_types=1);

namespace TBoileau\TwitchApi\Tests\Command;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpClient\HttpClient;
use TBoileau\TwitchApi\Command\InstallTwitchCliCommand;
use TBoileau\TwitchApi\Command\ServeTwitchMockServerCommand;

final class ServeTwitchMockServerTest extends TestCase
{
    private Application $application;

    protected function setUp(): void
    {
        $this->application = new Application();

        $this->application->add(
            new InstallTwitchCliCommand(
                HttpClient::create(),
                sprintf('%s/twitch', sys_get_temp_dir())
            )
        );

        $this->application->add(
            new ServeTwitchMockServerCommand(
                sprintf('%s/twitch', sys_get_temp_dir()),
                8080
            )
        );
    }

    /**
     * @test
     */
    public function shouldSuccessfullyServeTwitchMockServer(): void
    {
        $command = $this->application->find('twitch:serve');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);
        $commandTester->assertCommandIsSuccessful();
    }
}
