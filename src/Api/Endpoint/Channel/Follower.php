<?php

declare(strict_types=1);

namespace TBoileau\TwitchApi\Api\Endpoint\Channel;

class Follower
{
    public function __construct(
        public \DateTimeInterface $followedAt,
        public string $userId,
        public string $userLogin,
        public string $userName,
    ) {
    }
}
