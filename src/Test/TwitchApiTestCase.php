<?php

declare(strict_types=1);

namespace TBoileau\TwitchApi\Test;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\HttpClient;
use TBoileau\TwitchApi\Api\Endpoint\AbstractOperations;
use TBoileau\TwitchApi\Api\TwitchApiFactory;
use TBoileau\TwitchApi\Api\TwitchApiInterface;

abstract class TwitchApiTestCase extends TestCase
{
    protected TwitchApiInterface $twitchApi;

    protected string $userId;

    protected function createApi(): void
    {
        $httpClient = HttpClient::create([
            'base_uri' => $_ENV['TWITCH_API_HOST'],
        ]);

        /** @var array{data: non-empty-array<array-key, array{ID: string, Secret: string}>} $clients */
        $clients = $httpClient->request('GET', '/units/clients')->toArray();

        /** @var array{data: non-empty-array<array-key, array{id: string}>} $clients */
        $users = $httpClient->request('GET', '/units/users')->toArray();

        $this->userId = $users['data'][0]['id'];

        /** @var array{access_token: string} $clients */
        $token = $httpClient->request('POST', '/auth/authorize', [
            'query' => [
                'client_id' => $clients['data'][0]['ID'],
                'client_secret' => $clients['data'][0]['Secret'],
                'grant_type' => 'user_token',
                'user_id' => $this->userId,
                'scope' => implode(
                    ' ',
                    array_map(
                        /**
                         * @param class-string<AbstractOperations> $operations
                         */
                        fn(string $operations): string => implode(' ', $operations::getScopes()),
                        TwitchApiFactory::OPERATIONS
                    )
                )
            ]
        ])->toArray(false);

        $this->twitchApi = TwitchApiFactory::create(
            new \TBoileau\TwitchApi\HttpClient(
                $httpClient,
                sprintf('%s%s', $_ENV['TWITCH_API_HOST'], $_ENV['TWITCH_API_BASE_URI']),
                $token['access_token'],
                $clients['data'][0]['ID']
            )
        );
    }

    protected function call(string $operationName, array $parameters = []): mixed
    {
        [$group, $operation] = explode('::', $operationName);

        return $this->twitchApi->{$group}->{$operation}(...$parameters);
    }
}
