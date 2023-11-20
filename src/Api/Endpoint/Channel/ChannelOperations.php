<?php

declare(strict_types=1);

namespace TBoileau\TwitchApi\Api\Endpoint\Channel;

use TBoileau\TwitchApi\Api\Endpoint\Pagination;

class ChannelOperations
{
    public function getFollowers(string $broadcasterId, int $first, ?string $cursor = null): Pagination
    {
        return new Pagination(
            data: array_fill(0, $cursor === null ? $first : 10, new Follower(new \DateTimeImmutable(), '1', 'foo', 'bar')),
            total: 30,
            next: \Closure::bind(
                fn (): Pagination => $this->getFollowers($broadcasterId, $first, 'page-2'),
                $this
            )
        );
    }
}
