<?php

declare(strict_types=1);

namespace TBoileau\TwitchApi\Tests\Command;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use TBoileau\TwitchApi\Command\GetTwitchCliCommand;

final class GetTwitchCliCommandTest extends TestCase
{
    private Application $application;

    protected function setUp(): void
    {
        $this->application = new Application();

        $expectedRequests = [
            function ($method, $url): MockResponse {
                $pattern = '/^https:\/\/github\.com\/twitchdev\/twitch-cli\/releases\/download\/v(?<release>\d+\.\d+\.\d+)\/twitch-cli_\d+\.\d+\.\d+_(?<distribution>[a-zA-Z0-9_]+)\.(tar.gz|exe)$/';

                self::assertMatchesRegularExpression($pattern, $url);

                preg_match($pattern, $url, $matches);

                ['release' => $release, 'distribution' => $distribution] = $matches;

                $baseName = sprintf('twitch-cli_%s_%s', $release, $distribution);
                $filename = sprintf(
                    '%s%s',
                    $baseName,
                    GetTwitchCliCommand::TWITCH_CLI_DISTRIBUTIONS[$distribution]['ext']
                );

                $localFilename = sprintf('%s/fixtures/%s', __DIR__, $filename);

                if (!file_exists($localFilename)) {
                    return new MockResponse('', ['http_code' => 404]);
                }

                self::assertSame('GET', $method);

                $stream = fopen($localFilename, 'r');
                $body = fread($stream, filesize($localFilename));
                fclose($stream);

                return new MockResponse($body);
            },
        ];

        $this->application->add(
            new GetTwitchCliCommand(
                new MockHttpClient($expectedRequests),
                sys_get_temp_dir()
            )
        );
    }

    /**
     * @test
     */
    public function shouldSuccessfullyGetTwitchCli(): void
    {
        $command = $this->application->find('twitch:cli');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);
        $commandTester->assertCommandIsSuccessful();
    }

    /**
     * @dataProvider provideReleaseAndDistribution
     *
     * @test
     */
    public function shouldFailed(string $release, string $distribution): void
    {
        $command = $this->application->find('twitch:cli');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['-r' => $release, '-d' => $distribution]);
        self::assertSame(Command::FAILURE, $commandTester->getStatusCode());
    }

    public static function provideReleaseAndDistribution(): \Generator
    {
        yield 'invalid version' => ['fail', ''];
        yield 'invalid distribution' => ['1.1.21', 'fail'];
        yield 'download failed' => ['1.0.0', 'Linux_x86_64'];
        yield 'extraction error' => ['0.0.0', 'Linux_x86_64'];
        yield 'executable not working' => ['1.1.21', 'Darwin_x86_64'];
    }
}
