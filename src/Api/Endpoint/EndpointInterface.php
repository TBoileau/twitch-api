<?php

declare(strict_types=1);

namespace TBoileau\TwitchApi\Api\Endpoint;

interface EndpointInterface
{
    public function supports(string $uri): bool;

    public static function getScope(): string;
}
