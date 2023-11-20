<?php

declare(strict_types=1);

use TBoileau\TwitchApi\Api\Endpoint\Bits\BitsOperations;
use TBoileau\TwitchApi\Api\Endpoint\Channel\ChannelOperations;

return [
    ChannelOperations::getName() => new ChannelOperations(),
    BitsOperations::getName() => new BitsOperations(),
];
