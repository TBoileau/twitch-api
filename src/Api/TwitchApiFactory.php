<?php

declare(strict_types=1);

namespace TBoileau\TwitchApi\Api;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use TBoileau\TwitchApi\Api\Endpoint\AbstractOperations;

final class TwitchApiFactory
{
    /**
     * @param HttpClientInterface $httpClient
     * @param array<string, AbstractOperations> $groupsOfOperations
     * @return TwitchApiInterface
     */
    public static function create(HttpClientInterface $httpClient, array $groupsOfOperations): TwitchApiInterface
    {
        array_walk(
            $groupsOfOperations,
            fn(AbstractOperations $operations) => $operations->setHttpClient($httpClient)
        );

        return new TwitchApi($groupsOfOperations);
    }
}
