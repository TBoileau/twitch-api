<?php

declare(strict_types=1);

namespace TBoileau\TwitchApi\Api;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use TBoileau\TwitchApi\Api\Endpoint\AbstractOperations;
use TBoileau\TwitchApi\Api\Endpoint\Bits\BitsOperations;
use TBoileau\TwitchApi\Api\Endpoint\Channel\ChannelOperations;

final class TwitchApiFactory
{
    public static function create(HttpClientInterface $httpClient): TwitchApiInterface
    {
        /** @var array<string, AbstractOperations> $groupsOfOperations */
        $groupsOfOperations = [
            ChannelOperations::getName() => new ChannelOperations(),
            BitsOperations::getName() => new BitsOperations(),
        ];

        array_walk(
            $groupsOfOperations,
            fn(AbstractOperations $operations) => $operations->setHttpClient($httpClient)
        );

        return new TwitchApi($groupsOfOperations);
    }
}
