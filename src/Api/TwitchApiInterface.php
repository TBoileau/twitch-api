<?php

declare(strict_types=1);

namespace TBoileau\TwitchApi\Api;

use TBoileau\TwitchApi\Api\Endpoint\EndpointInterface;

interface TwitchApiInterface
{
    /**
     * @param class-string<EndpointInterface> $endpointClass
     * @param array $options
     * @return mixed
     */
    public function call(string $endpointClass, array $options): mixed;
}
