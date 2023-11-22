<?php

declare(strict_types=1);

namespace TBoileau\TwitchApi\Tests\Api\Endpoint;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use TBoileau\TwitchApi\Api\Endpoint\Channel\Follower;
use TBoileau\TwitchApi\Api\Endpoint\Subscriptions\Subscription;
use TBoileau\TwitchApi\Api\Endpoint\Subscriptions\SubscriptionsOperations;

class SubscriptionsOperationsTest extends TestCase
{
    #[Test]
    public function shouldGetBroadcasterSubscriptions(): void
    {
        $subscriptionsOperations = new SubscriptionsOperations();

        $callback = function ($method, $url, $options): MockResponse {
            $subscribers = array_fill(
                0,
                30,
                [
                    'broadcaster_id' => '1',
                    'broadcaster_login' => 'foo',
                    'broadcaster_name' => 'bar',
                    'gifter_id' => '1',
                    'gifter_login' => 'foo',
                    'gifter_name' => 'bar',
                    'is_gift' => true,
                    'plan_name' => 'foo',
                    'tier' => '1000 - Tier 1',
                    'user_id' => '1',
                    'user_login' => 'foo',
                    'user_name' => 'bar',
                ]
            );
            $first = $options['query']['first'] ?? 20;
            $cursor = $options['query']['after'] ?? $options['query']['before'] ?? 'page-1';

            $offset = match ($cursor) {
                'page-1' => 0,
                'page-2' => 20,
                default => 0
            };

            $nextCursor = match ($cursor) {
                'page-1' => 'page-2',
                default => null
            };

            $previousCursor = match ($cursor) {
                'page-2' => 'page-1',
                default => null
            };

            $responseData = [
                'total' => count($subscribers),
                'data' => array_slice($subscribers, $offset, $first),
            ];

            if ($nextCursor !== null) {
                $responseData['pagination'] = [
                    'cursor' => $nextCursor
                ];
            }

            if ($previousCursor !== null) {
                $responseData['pagination'] = [
                    'cursor' => $previousCursor
                ];
            }

            return new MockResponse(json_encode($responseData));
        };

        $httpClient = new MockHttpClient($callback);

        $subscriptionsOperations->setHttpClient($httpClient);

        $pagination = $subscriptionsOperations->getBroadcasterSubscriptions('123', 20);

        self::assertCount(30, $pagination);
        self::assertCount(20, $pagination->getIterator());
        self::assertContainsOnlyInstancesOf(Subscription::class, $pagination->getIterator());
        $pagination = $pagination->next();
        self::assertCount(10, $pagination->getIterator());
        $pagination = $pagination->previous();
        self::assertCount(20, $pagination->getIterator());
    }
}
