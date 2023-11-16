<?php

declare(strict_types=1);

namespace TBoileau\TwitchApi\Api;

use Psr\Container\ContainerInterface;

class TwitchApi implements TwitchApiInterface
{
    public function __construct(private ContainerInterface $endpoints)
    {
    }

    public function call(string $endpointClass, array $options): mixed
    {
        return $this->endpoints->get($endpointClass)->call($options);
    }
}
