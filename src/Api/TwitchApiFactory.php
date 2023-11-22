<?php

declare(strict_types=1);

namespace TBoileau\TwitchApi\Api;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use TBoileau\TwitchApi\Api\Endpoint\Bits\BitsOperations;
use TBoileau\TwitchApi\Api\Endpoint\Channel\ChannelOperations;
use TBoileau\TwitchApi\Api\Endpoint\Subscriptions\SubscriptionsOperations;

final class TwitchApiFactory
{
    public const OPERATIONS = [
        ChannelOperations::class,
        BitsOperations::class,
        SubscriptionsOperations::class,
    ];

    public static function create(HttpClientInterface $httpClient): TwitchApiInterface
    {
        return new TwitchApi(
            array_merge(
                ...array_map(
                    function (string $operationsClass) use ($httpClient): array {
                        $operations = new $operationsClass();
                        $operations->setHttpClient($httpClient);

                        return [$operations::getName() => $operations];
                    },
                    self::OPERATIONS
                )
            )
        );
    }
}
