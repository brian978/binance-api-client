<?php

declare(strict_types=1);

namespace BinanceApi\RateLimiter;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\RateLimiter\LimiterInterface;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\RateLimiter\Storage\StorageInterface;

class RateLimiter implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private array $rateLimitHeaders = [
        'X-SAPI-USED-IP-WEIGHT-',
        'X-SAPI-USED-UID-WEIGHT-',
        'X-MBX-USED-WEIGHT-'
    ];

    private array $maxWeightMap = [
        'X-SAPI-USED-IP-WEIGHT-' => SapiEndpointWeight::IP_WEIGHT,
        'X-SAPI-USED-UID-WEIGHT-' => SapiEndpointWeight::UID_WEIGHT,
        'X-MBX-USED-WEIGHT-' => ApiEndpointWeight::IP_WEIGHT,
    ];

    private array $intervalMap = [
        'S' => 'second',
        'M' => 'minute',
        'H' => 'hour',
        'D' => 'day',
    ];

    private int $maxWeight;
    private string $interval;
    private int $calculatedWeightUsed;
    private int $reportedWeightUsed;

    private string $limiterKey;

    private StorageInterface $storage;
    private LockFactory $lockFactory;
    private LimiterInterface $limiter;

    public function __construct(StorageInterface $storage, LockFactory $lockFactory)
    {
        $this->storage = $storage;
        $this->lockFactory = $lockFactory;
    }

    public function setLimiterKey(string $limiterKey): self
    {
        $this->limiterKey = $limiterKey;
        return $this;
    }

    public function preventSpam(int $weight): void
    {
        if (!isset($this->limiter)) {
            $this->calculatedWeightUsed = $weight;
            return;
        }

        $limit = $this->limiter->consume($weight);
        $this->calculatedWeightUsed = $this->maxWeight - $limit->getRemainingTokens();

        while (false === $limit->isAccepted()) {
            $this->logger->warning('Request limit reached. Waiting...');
            $limit->wait();
            $limit = $this->limiter->consume($weight);
        }
    }

    public function syncRateLimitSpecs(array $headers): void
    {
        $this->parseHeaders($headers);

        if (!isset($this->interval)) {
            $message = 'Could not detect rate limit interval';

            $this->logger->error($message);
            throw new \RuntimeException($message);
        }

        if (!isset($this->limiter)) {
            $this->initialize();
        }

        $this->logger->debug('API reported used weight: ' . $this->reportedWeightUsed);
        $this->logger->debug('Calculated used weight: ' . $this->calculatedWeightUsed);

        if ($this->reportedWeightUsed !== $this->calculatedWeightUsed) {
            $this->logger->warning('Rate limit mismatch. Resetting to ' . $this->reportedWeightUsed);

            $this->limiter->reset();
            $this->limiter->consume($this->reportedWeightUsed);
        }
    }

    private function setRateLimitInterval(string $limitHeader, string $headerName): void
    {
        $pattern = "/$limitHeader(?P<intervalNum>[0-9]+)(?P<intervalLetter>S|M|H|D)/";

        if (preg_match($pattern, $headerName, $matches)) {
            $this->interval = $matches['intervalNum'] . ' ' . $this->intervalMap[$matches['intervalLetter']];
        }

        $this->logger->debug('Detected cooldown interval: ' . $this->interval);
    }

    private function parseHeaders(array $headers): void
    {
        foreach ($headers as $headerName => $value) {
            $headerName = strtoupper($headerName);
            foreach ($this->rateLimitHeaders as $limitHeader) {
                if (str_starts_with($headerName, $limitHeader)) {
                    if (!isset($this->interval)) {
                        $this->setRateLimitInterval($limitHeader, $headerName);
                    }

                    $this->reportedWeightUsed = (int)current($value);
                    $this->maxWeight = $this->maxWeightMap[$limitHeader];
                    break;
                }
            }
        }
    }

    private function initialize(): self
    {
        $config = [
            'id' => 'binance-api-rate-limiter',
            'policy' => 'fixed_window',
            'limit' => $this->maxWeight,
            'interval' => $this->interval,
        ];

        $limiterWildcard = $this->maxWeight . '-' . str_replace(' ', '', $this->interval);

        $factory = new RateLimiterFactory($config, $this->storage, $this->lockFactory);

        $this->limiter = $factory->create('binance-api-limiter-' . $limiterWildcard . '-' . $this->limiterKey);
        $this->limiter->consume($this->calculatedWeightUsed); // Consume weight that is already used

        return $this;
    }
}
