<?php

declare(strict_types=1);

namespace TBoileau\TwitchApi\PHPUnit;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class TwitchApiTestCase extends TestCase
{
    protected static ?HttpClientInterface $httpClient = null;

    protected function createHttpClient(): void
    {
        if (null === self::$httpClient) {
            static::$httpClient = HttpClient::create()->withOptions([
                'base_uri' => sprintf('http://localhost:%s/mock/', $_ENV['PORT'] ?? 8080),
            ]);
        }
    }

    /**
     * @return array{user_id: string}
     */
    protected function authorize(): array
    {
        if (self::$httpClient === null) {
            $this->createHttpClient();
        }

        /** @var array{data: array<array-key, array{ID: string, Secret: string}>} $response */
        $clients = self::$httpClient->request('GET', '/units/clients')->toArray();

        /** @var array{data: array<array-key, array{id: string}>} $response */
        $users = self::$httpClient->request('GET', '/units/users')->toArray();

        /** @var array{access_token: string}} $token */
        $token = self::$httpClient->request('POST', '/auth/authorize', [
            'query' => [
                'client_id' => $clients['data'][0]['ID'],
                'client_secret' => $clients['data'][0]['Secret'],
                'grant_type' => 'user_token',
                'user_id' => $users['data'][0]['id'],
                'scope' => 'channel:edit:commercial'
            ],
        ])->toArray();

        self::$httpClient = self::$httpClient->withOptions([
            'headers' => [
                'Authorization' => sprintf('Bearer %s', $token['access_token']),
                'Client-Id' => $clients['data'][0]['ID'],
                'Content-Type' => 'application/json',
            ],
        ]);

        return ['user_id' => $users['data'][0]['id']];
    }
}
