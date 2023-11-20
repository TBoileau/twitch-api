<?php

declare(strict_types=1);

namespace TBoileau\TwitchApi;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\ResponseStreamInterface;

class HttpClient implements HttpClientInterface
{
    public function __construct(
        private HttpClientInterface $decoratedHttpClient,
        private readonly string     $baseUri,
        private readonly string     $accessToken,
        private readonly string     $clientId,
    )
    {
        $this->decoratedHttpClient = $decoratedHttpClient->withOptions([
            'base_uri' => $baseUri,
            'headers' => [
                'Authorization' => sprintf('Bearer %s', $accessToken),
                'Client-Id' => $clientId,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    public function withOptions(array $options): static
    {
        return new self(
            $this->decoratedHttpClient->withOptions($options),
            $this->baseUri,
            $this->accessToken,
            $this->clientId
        );
    }

    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        return $this->decoratedHttpClient->request($method, $url, $options);
    }

    public function stream(iterable|ResponseInterface $responses, float $timeout = null): ResponseStreamInterface
    {
        return $this->decoratedHttpClient->stream($responses, $timeout);
    }
}
