<?php

declare(strict_types=1);

namespace TBoileau\TwitchApi\Api\Endpoint;

use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class AbstractOperations
{
    protected ?HttpClientInterface $httpClient = null;

    abstract public static function getName(): string;

    /**
     * @return array<string>
     */
    abstract public static function getScopes(): array;

    public function setHttpClient(HttpClientInterface $httpClient): static
    {
        if ($this->httpClient === null) {
            $this->httpClient = $httpClient;
        }

        return $this;
    }
}
