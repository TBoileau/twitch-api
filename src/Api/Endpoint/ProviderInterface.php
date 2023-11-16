<?php

declare(strict_types=1);

namespace TBoileau\TwitchApi\Api\Endpoint;

interface ProviderInterface extends EndpointInterface
{
    public function process(array $query = []): array;
}
