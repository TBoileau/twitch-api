<?php

declare(strict_types=1);

namespace TBoileau\TwitchApi\Tests\Api;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use Symfony\Component\HttpClient\HttpClient;
use TBoileau\TwitchApi\Api\Endpoint\AbstractOperations;
use TBoileau\TwitchApi\Api\Endpoint\Channel\ChannelOperations;
use TBoileau\TwitchApi\Api\TwitchApi;

class TwitchApiTest extends TestCase
{
    private TwitchApi $twitchApi;

    protected function setUp(): void
    {
        $channelOperations = new ChannelOperations();

        $channelOperations->setHttpClient(HttpClient::create());

        $this->twitchApi = new TwitchApi([
            $channelOperations::getName() => $channelOperations,
        ]);
    }

    /**
     * @param class-string<AbstractOperations> $operationsClass
     * @return void
     */
    #[Test]
    #[DataProvider('provideOperations')]
    public function shouldReturnGroupOfOperations(string $operationsClass): void
    {
        $this->assertInstanceOf($operationsClass, $this->twitchApi->Channel);
    }

    /**
     * @return \Generator<string, array{class-string<AbstractOperations>}>
     */
    public static function provideOperations(): \Generator
    {
        yield 'Channel' => [ChannelOperations::class];
    }
}
