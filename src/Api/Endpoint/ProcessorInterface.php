<?php

declare(strict_types=1);

namespace TBoileau\TwitchApi\Api\Endpoint;

interface ProcessorInterface extends EndpointInterface
{
    public function process(array $body = [], array $query = []): array;
}
