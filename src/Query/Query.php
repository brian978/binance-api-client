<?php

declare(strict_types=1);

namespace BinanceApi\Query;

use BinanceApi\Client;
use Psr\Http\Message\ResponseInterface;

abstract class Query implements QueryInterface
{
    protected Client $client;

    // Endpoint properties
    protected string $method;
    protected string $resource;
    protected int $weight;

    // Request properties
    protected array $required = [];

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    abstract protected function makeRequest(): ResponseInterface;

    /**
     * @param \BinanceApi\Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;

        if (!isset($this->method)) {
            throw new \RuntimeException('Query method not set!');
        }

        if (!isset($this->resource)) {
            throw new \RuntimeException('Query resource not set!');
        }

        if (!isset($this->weight)) {
            throw new \RuntimeException('Query weight not set!');
        }

        $this->client->setRequestWeight($this->weight);
    }

    protected function payload(): array
    {
        $payload = array_diff_key(get_object_vars($this), get_class_vars(self::class));

        foreach ($this->required as $item) {
            if (!isset($payload[$item])) {
                throw new \RuntimeException(
                    'Mandatory parameter not found: ' . $item
                );
            }
        }

        return $payload;
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function execute(): array
    {
        $response = $this->makeRequest();

        return json_decode((string)$response->getBody(), true);
    }
}
