<?php

declare(strict_types=1);

namespace TBoileau\TwitchApi\Api;

use TBoileau\TwitchApi\Api\Endpoint\Bits\BitsOperations;
use TBoileau\TwitchApi\Api\Endpoint\Channel\ChannelOperations;
use TBoileau\TwitchApi\Api\Endpoint\Subscriptions\SubscriptionsOperations;
use TBoileau\TwitchApi\HttpClient;
use Symfony\Component\HttpClient\HttpClient as SymfonyHttpClient;

final class TwitchApiFactory
{
    public const OPERATIONS = [
        ChannelOperations::class,
        BitsOperations::class,
        SubscriptionsOperations::class,
    ];

    public static function create(string $accessToken, string $clientId): TwitchApiInterface
    {
        $httpClient = new HttpClient(
            SymfonyHttpClient::create(),
            sprintf('%s%s', $_ENV['TWITCH_API_HOST'], $_ENV['TWITCH_API_BASE_URI']),
            $accessToken,
            $clientId
        );

        return new TwitchApi(
            array_merge(
                ...array_map(
                    function (string $operationsClass): array {
                        $operations = new $operationsClass();

                        return [$operations::getName() => $operations];
                    },
                    self::OPERATIONS
                )
            ),
            $httpClient
        );
    }
}
