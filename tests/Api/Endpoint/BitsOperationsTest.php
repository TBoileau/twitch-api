<?php

declare(strict_types=1);

namespace TBoileau\TwitchApi\Tests\Api\Endpoint;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use TBoileau\TwitchApi\Api\Endpoint\Bits\BitsOperations;
use TBoileau\TwitchApi\Api\Endpoint\Bits\Leader;

class BitsOperationsTest extends TestCase
{
    #[Test]
    public function shouldGetLeaderboard(): void
    {
        $bitsOperations = new BitsOperations();

        $httpClient = new MockHttpClient(new MockResponse(json_encode([
            'data' => [
                [
                    'user_id' => '123',
                    'user_login' => 'foo',
                    'user_name' => 'foo',
                    'rank' => 1,
                    'score' => 100,
                ],
                [
                    'user_id' => '456',
                    'user_login' => 'bar',
                    'user_name' => 'bar',
                    'rank' => 2,
                    'score' => 50,
                ],
            ],
            'date_range' => [
                'started_at' => '2021-01-01T00:00:00Z',
                'ended_at' => '2021-01-02T00:00:00Z',
            ],
            'total' => 2,
        ])));

        $bitsOperations->setHttpClient($httpClient);

        $leaderboard = $bitsOperations->getLeaderboard();

        self::assertCount(2, $leaderboard);
        self::assertCount(2, $leaderboard->getIterator());
        self::assertContainsOnlyInstancesOf(Leader::class, $leaderboard->getIterator());
    }
}
