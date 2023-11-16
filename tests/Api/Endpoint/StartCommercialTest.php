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
        $this->createApi();

        $response = $this->call(StartCommercial::class, [
            'json' => [
                'broadcaster_id' => $this->userId,
                'length' => 30,
            ]
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
