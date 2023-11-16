<?php

declare(strict_types=1);

namespace TBoileau\TwitchApi\Api\Endpoint;

use Symfony\Contracts\HttpClient\HttpClientInterface;

final class StartCommercial implements ProcessorInterface
{
    private const URI = 'channels/commercial';

    public function __construct(private HttpClientInterface $httpClient)
    {
    }

    public function supports(string $uri): bool
    {
        return self::URI === $uri;
    }

    public static function getScope(): string
    {
        return 'channel:edit:commercial';
    }

    /**
     * @param array{broadcaster_id: string, length: int} $body
     * @param array<empty, empty> $query
     * @return array{data: array<array-key, array{length: int, message: string, retry_after: int}>}
     */
    public function process(array $body = [], array $query = []): array
    {
        return $this->httpClient->request('POST', self::URI, [
            'json' => $body,
            'query' => $query,
        ])->toArray();
    }
}
