<?php

declare(strict_types=1);

namespace TBoileau\TwitchApi\PHPUnit;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use TBoileau\TwitchApi\Api\Endpoint\EndpointInterface;
use TBoileau\TwitchApi\Api\TwitchApi;
use TBoileau\TwitchApi\Api\TwitchApiInterface;

abstract class TwitchApiTestCase extends TestCase
{
    protected ?HttpClientInterface $httpClient = null;

    protected ?string $userId = null;

    protected ?TwitchApiInterface $twitchApi = null;
    
    protected function call(string $endpointClass, array $options): array
    {
        return $this->twitchApi->call($endpointClass, $options);
    }

    protected function createApi(): void
    {
        $httpClient = HttpClient::create()->withOptions([
            'base_uri' => sprintf('http://localhost:%s/mock/', $_ENV['PORT'] ?? 8080),
        ]);

        $this->authorize($httpClient);

        $this->twitchApi = new TwitchApi(new class($httpClient) implements ContainerInterface {
            public function __construct(private HttpClientInterface $httpClient)
            {
            }

            /**
             * @param class-string<EndpointInterface> $id
             * @return EndpointInterface
             */
            public function get(string $id): EndpointInterface
            {
                $endpoint = new $id();
                $endpoint->setHttpClient($this->httpClient);

                return $endpoint;
            }

            public function has(string $id): bool
            {
                $reflectionClass = new \ReflectionClass($id);

                return $reflectionClass->implementsInterface(EndpointInterface::class);
            }
        });
    }

    protected function authorize(HttpClientInterface &$httpClient): void
    {
        /** @var array{data: array<array-key, array{ID: string, Secret: string}>} $response */
        $clients = $httpClient->request('GET', '/units/clients')->toArray();

        /** @var array{data: array<array-key, array{id: string}>} $response */
        $users = $httpClient->request('GET', '/units/users')->toArray();

        /** @var array{access_token: string}} $token */
        $token = $httpClient->request('POST', '/auth/authorize', [
            'query' => [
                'client_id' => $clients['data'][0]['ID'],
                'client_secret' => $clients['data'][0]['Secret'],
                'grant_type' => 'user_token',
                'user_id' => $users['data'][0]['id'],
                'scope' => 'channel:edit:commercial'
            ],
        ])->toArray();

        $httpClient = $httpClient->withOptions([
            'headers' => [
                'Authorization' => sprintf('Bearer %s', $token['access_token']),
                'Client-Id' => $clients['data'][0]['ID'],
                'Content-Type' => 'application/json',
            ],
        ]);

        $this->userId= $users['data'][0]['id'];
    }
}
