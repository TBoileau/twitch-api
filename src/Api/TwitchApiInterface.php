<?php

declare(strict_types=1);

namespace TBoileau\TwitchApi\Api;

use TBoileau\TwitchApi\Api\Endpoint\AbstractOperations;
use TBoileau\TwitchApi\Api\Endpoint\Bits\BitsOperations;
use TBoileau\TwitchApi\Api\Endpoint\Channel\ChannelOperations;
use TBoileau\TwitchApi\Api\Endpoint\Subscriptions\SubscriptionsOperations;

/**
 * @property ChannelOperations $Channel
 * @property SubscriptionsOperations $Subscriptions
 * @property BitsOperations $Bits
 */
interface TwitchApiInterface
{
    public function __get(string $name): AbstractOperations;
}
