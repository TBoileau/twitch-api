<?php

declare(strict_types=1);

namespace TBoileau\TwitchApi\Api\Endpoint\Bits;

class Leader
{
    public function __construct(
        public string $userId,
        public string $userLogin,
        public string $userName,
        public int    $rank,
        public int    $score,
    )
    {
    }
}
