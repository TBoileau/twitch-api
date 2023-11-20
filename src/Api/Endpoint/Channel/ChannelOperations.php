<?php

declare(strict_types=1);

namespace TBoileau\TwitchApi\Api\Endpoint\Channel;

use TBoileau\TwitchApi\Api\Endpoint\AbstractOperations;
use TBoileau\TwitchApi\Api\Endpoint\Pagination;

class ChannelOperations extends AbstractOperations
{
    /**
     * @return Pagination<Follower>
     */
    public function getFollowers(string $broadcasterId, int $first, ?string $cursor = null): Pagination
    {
        $response = $this->httpClient->request('GET', 'channels/followers', [
            'query' => [
                'broadcaster_id' => $broadcasterId,
                'first' => $first,
                'after' => $cursor,
            ],
        ])->toArray();

        return new Pagination(
            data: array_map(
                fn (array $follower): Follower => new Follower(new \DateTimeImmutable($follower['followed_at']), $follower['user_id'], $follower['user_login'], $follower['user_name']),
                $response['data']
            ),
            total: $response['total'],
            next: ($response['pagination'] ?? null) === null
                ? null
                :\Closure::bind(
                    fn (): Pagination => $this->getFollowers($broadcasterId, $first, $response['pagination']['cursor'] ?? null),
                    $this
                )
        );
    }
}
