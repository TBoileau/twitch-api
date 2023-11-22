<?php

declare(strict_types=1);

namespace TBoileau\TwitchApi\Tests\Api\Endpoint;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use TBoileau\TwitchApi\Api\Endpoint\Channel\ChannelOperations;
use TBoileau\TwitchApi\Api\Endpoint\Channel\Follower;

class ChannelOperationsTest extends TestCase
{
    #[Test]
    public function shouldGetFollowers(): void
    {
        $channelOperations = new ChannelOperations();

        $callback = function ($method, $url, $options): MockResponse {
            $followers = array_fill(
                0,
                30,
                [
                    'followed_at' => '2021-01-01T00:00:00Z',
                    'user_id' => '1',
                    'user_login' => 'foo',
                    'user_name' => 'bar'
                ]
            );
            $first = $options['query']['first'] ?? 20;
            $cursor = $options['query']['after'] ?? 'page-1';

            $offset = match ($cursor) {
                'page-1' => 0,
                'page-2' => 20,
                default => 0
            };

            $nextCursor = match ($cursor) {
                'page-1' => 'page-2',
                default => null
            };

            $responseData = [
                'total' => count($followers),
                'data' => array_slice($followers, $offset, $first),
            ];

            if ($nextCursor !== null) {
                $responseData['pagination'] = [
                    'cursor' => $nextCursor
                ];
            }

            return new MockResponse(json_encode($responseData));
        };

        $httpClient = new MockHttpClient($callback);

        $channelOperations->setHttpClient($httpClient);

        $pagination = $channelOperations->getFollowers('123', 20);

        self::assertCount(30, $pagination);
        self::assertCount(20, $pagination->getIterator());
        self::assertContainsOnlyInstancesOf(Follower::class, $pagination->getIterator());

        $pagination = $pagination->next();

        self::assertCount(10, $pagination->getIterator());
        self::assertNull($pagination->next());
    }
}
