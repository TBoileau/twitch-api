<?php

declare(strict_types=1);

namespace TBoileau\TwitchApi\Api\Endpoint;

use Symfony\Contracts\HttpClient\HttpClientInterface;

trait EndpointHttpTrait
{
    private HttpClientInterface $httpClient;

    public function setHttpClient(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }
}
