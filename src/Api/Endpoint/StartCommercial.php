<?php

declare(strict_types=1);

namespace TBoileau\TwitchApi\Api\Endpoint;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class StartCommercial implements EndpointInterface
{
    use EndpointHttpTrait;

    private const URI = 'channels/commercial';

    public function supports(string $uri): bool
    {
        return self::URI === $uri;
    }

    public static function getScope(): string
    {
        return 'channel:edit:commercial';
    }

    /**
     *
     * @param array{json: array{broadcaster_id: string, length: int}, query: array<empty, empty>} $options
     * @return array{data: array<array-key, array{length: int, message: string, retry_after: int}>}
     */
    public function call(array $options = []): array
    {

        return $this->httpClient->request('POST', self::URI, $options)->toArray();
    }
}
