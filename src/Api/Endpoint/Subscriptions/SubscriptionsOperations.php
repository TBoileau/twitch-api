<?php

declare(strict_types=1);

namespace TBoileau\TwitchApi\Api\Endpoint\Subscriptions;

use Closure;
use TBoileau\TwitchApi\Api\Endpoint\AbstractOperations;
use TBoileau\TwitchApi\Api\Endpoint\Channel\Follower;
use TBoileau\TwitchApi\Api\Endpoint\Pagination;

class SubscriptionsOperations extends AbstractOperations
{
    public static function getName(): string
    {
        return 'Subscriptions';
    }

    public static function getScopes(): array
    {
        return ['channel:read:subscriptions'];
    }

    /**
     * @return Pagination<Subscription>
     */
    public function getBroadcasterSubscriptions(string $broadcasterId, int $first = 20, ?string $after = null, ?string $before = null): Pagination
    {
        $response = $this->httpClient->request('GET', 'subscriptions', [
            'query' => [
                'broadcaster_id' => $broadcasterId,
                'first' => $first,
                'after' => $after,
                'before' => $before,
            ],
        ])->toArray(false);

        return new Pagination(
            data: array_map(
                fn(array $subscription): Subscription => new Subscription(
                    $subscription['broadcaster_id'],
                    $subscription['broadcaster_login'],
                    $subscription['broadcaster_name'],
                    $subscription['gifter_id'] ?? null,
                    $subscription['gifter_login'] ?? null,
                    $subscription['gifter_name'] ?? null,
                    $subscription['is_gift'],
                    $subscription['plan_name'],
                    $subscription['tier'],
                    $subscription['user_id'],
                    $subscription['user_login'],
                    $subscription['user_name']
                ),
                $response['data']
            ),
            total: $response['total'],
            next: ($response['pagination'] ?? null) === null
                ? null
                : Closure::bind(
                    fn(): Pagination => $this->getBroadcasterSubscriptions(
                        $broadcasterId,
                        $first,
                        $response['pagination']['cursor'] ?? null,
                        null
                    ),
                    $this
                ),
            previous: ($response['pagination'] ?? null) === null
                ? null
                : Closure::bind(
                    fn(): Pagination => $this->getBroadcasterSubscriptions(
                        $broadcasterId,
                        $first,
                        null,
                        $response['pagination']['cursor'] ?? null
                    ),
                    $this
                )
        );
    }
}
