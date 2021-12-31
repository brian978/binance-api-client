<?php

declare(strict_types=1);

namespace BinanceApi\Query\Component;

use BinanceApi\Client;
use Psr\Http\Message\ResponseInterface;

/**
 * @property-read Client $client
 * @property-read string $resource
 * @property-read string $method
 * @method array payload()
 */
trait PublicQuery
{
    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function makeRequest(): ResponseInterface
    {
        return $this->client->request($this->method, $this->resource, $this->payload());
    }
}
