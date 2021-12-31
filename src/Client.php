<?php

declare(strict_types=1);

namespace BinanceApi;

use BinanceApi\RateLimiter\RateLimiter;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class Client implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private HttpClient $httpClient;
    private RateLimiter $rateLimiter;

    private string $apiKey;
    private string $apiSecret;
    private int $apiVersion = 0;

    // Rate limiting properties
    private int $requestWeight;

    public function __construct(
        HttpClient $httpClient,
        string $apiKey,
        string $apiSecret,
        RateLimiter $rateLimiter
    ) {
        $this->httpClient = $httpClient;
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;

        $this->rateLimiter = $rateLimiter;
        $this->rateLimiter->setLimiterKey(base64_encode($this->apiKey));
    }

    public function setRequestWeight(int $requestWeight): self
    {
        $this->requestWeight = $requestWeight;
        return $this;
    }

    private function prepareOptions(string $method, array $payload = [], array $headers = []): array
    {
        $headers['User-Agent'] = 'AcamarBinanceApiClient/' . ClientVersion::VERSION;
        $options = ['headers' => $headers];

        if ('GET' === $method) {
            $options['query'] = $payload;
        } else {
            $options['form_params'] = $payload;
        }

        return $options;
    }

    private function enrichPayload(array $payload): array
    {
        $payload['timestamp'] = $payload['timestamp'] ?? round(microtime(true) * 1000);
        $payload['recvWindow'] = $payload['recvWindow'] ?? 5000;
        $payload['signature'] = $this->createSignature($payload);

        return $payload;
    }

    private function createSignature(array $payload): string
    {
        $postData = utf8_encode(http_build_query($payload));

        return hash_hmac('sha256', $postData, $this->apiSecret);
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function request(string $method, string $uri, array $payload = [], array $headers = []): ResponseInterface
    {
        $this->logger->debug('Binance request URI: ' . $uri);

        $this->rateLimiter->preventSpam($this->requestWeight);

        // Cannot enhance the payload earlier due to recvWindow and spam prevention
        if (isset($headers['X-MBX-APIKEY'])) {
            $payload = $this->enrichPayload($payload);
        }

        return $this->makeRequest($method, $uri, $this->prepareOptions($method, $payload, $headers));
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function makeRequest(string $method, string $uri, array $options): ResponseInterface
    {
        // TODO: limit may already be reached when the first request is made, so it will fail

        $this->logger->debug('Binance request $options: ' . serialize($options));

        try {
            $response = $this->httpClient->request($method, $uri, $options);
            if ($response->getStatusCode() == 429) {
                $this->rateLimiter->preventSpam($this->requestWeight);
                $this->logger->error('Rate limit reached');
            }

            return $response;
        } catch (RequestException $e) {
            $this->logger->error('Binance request failed: ' . $e->getMessage());
            $this->logger->debug('Binance request trace: ' . $e->getTraceAsString());

            $response = $e->getResponse();

            throw $e;
        } finally {
            // Rate limiter should be synced with what the API is reporting in order to prevent rate limit hits
            $this->rateLimiter->syncRateLimitSpecs($response->getHeaders());

            if ($response->getStatusCode() == 429) {
                $this->rateLimiter->preventSpam($this->requestWeight);
                $this->logger->error('Rate limit reached');
            }
        }
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function privateRequest(string $method, string $resource, array $payload = []): ResponseInterface
    {
        $headers = [
            'X-MBX-APIKEY' => $this->apiKey,
            'Content-Type' => 'application/x-www-form-urlencoded; charset=utf-8'
        ];

        return $this->request($method, $resource, $payload, $headers);
    }
}
