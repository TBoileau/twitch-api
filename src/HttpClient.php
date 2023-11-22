<?php

declare(strict_types=1);

namespace TBoileau\TwitchApi;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
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
        try {
            return $this->decoratedHttpClient->request($method, $url, $options);
        } catch (ClientExceptionInterface $e) {
            if ($e->getResponse()->getStatusCode() === 401) {
                throw new TwitchUnauthorizedException($e->getMessage(), $e->getCode(), $e);
            }
        }
    }

    public function stream(iterable|ResponseInterface $responses, float $timeout = null): ResponseStreamInterface
    {
        return $this->decoratedHttpClient->stream($responses, $timeout);
    }
}
