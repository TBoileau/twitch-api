<?php

declare(strict_types=1);

namespace TBoileau\TwitchApi\Api\Endpoint\Bits;

class Leader
{
    public function __construct(
        string $userId,
        string $userLogin,
        string $userName,
        int    $rank,
        int    $score,
    )
    {
    }
}
