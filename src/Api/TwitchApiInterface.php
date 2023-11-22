<?php

declare(strict_types=1);

namespace TBoileau\TwitchApi\Api;

use TBoileau\TwitchApi\Api\Endpoint\AbstractOperations;
use TBoileau\TwitchApi\Api\Endpoint\Channel\ChannelOperations;

/**
 * @property ChannelOperations $channel
 */
interface TwitchApiInterface
{
    public function __get(string $name): AbstractOperations;
}
