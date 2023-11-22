<?php

declare(strict_types=1);

namespace TBoileau\TwitchApi\Api\Endpoint\Subscriptions;

class Subscription
{
    public function __construct(
        public string $broadcasterId,
        public string $broadcasterLogin,
        public string $broadcasterName,
        public ?string $gifterId,
        public ?string $gifterLogin,
        public ?string $gifterName,
        public bool $gift,
        public string $planName,
        public string $tier,
        public string $userId,
        public string $userLogin,
        public string $userName,
    ) {
    }
}
