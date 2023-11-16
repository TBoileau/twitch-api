<?php

declare(strict_types=1);

namespace TBoileau\TwitchApi\Tests\Api\Endpoint;

use TBoileau\TwitchApi\Api\Endpoint\StartCommercial;
use TBoileau\TwitchApi\PHPUnit\TwitchApiTestCase;

final class StartCommercialTest extends TwitchApiTestCase
{
    /**
     * @test
     */
    public function shouldSuccessfullyStartCommercial(): void
    {
        $this->createHttpClient();

        ['user_id' => $userId] = $this->authorize();

        $startCommercial = new StartCommercial(static::$httpClient);

        $response = $startCommercial->process([
            'broadcaster_id' => $userId,
            'length' => 30,
        ]);

        self::assertIsArray($response);
        self::assertArrayHasKey('data', $response);
        self::assertIsArray($response['data']);
        self::assertCount(1, $response['data']);
        self::assertArrayHasKey('length', $response['data'][0]);
        self::assertArrayHasKey('retry_after', $response['data'][0]);
        self::assertArrayHasKey('message', $response['data'][0]);
    }
}
