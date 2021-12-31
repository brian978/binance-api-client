<?php
/** @noinspection PhpPropertyOnlyWrittenInspection */

declare(strict_types=1);

namespace BinanceApi\Query\UserData;

use BinanceApi\Query\QueryInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class TradesHistoryQuery implements QueryInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    private TradesInfoQuery $tradesInfoQuery;

    private array $tradableAssets = [];
    private array $tradedAssets = [];

    public function __construct(TradesInfoQuery $tradesInfoQuery)
    {
        $this->tradesInfoQuery = $tradesInfoQuery;
    }

    public function setTradableAssets(array $tradableAssets): self
    {
        $this->tradableAssets = $tradableAssets;

        return $this;
    }

    public function setTradedAssets(array $tradedAssets): self
    {
        $this->tradedAssets = $tradedAssets;

        return $this;
    }

    public function addTradedAsset(string $symbol): self
    {
        $this->tradedAssets[] = $symbol;

        return $this;
    }

    public function execute(): array
    {
        $this->tradedAssets = array_unique($this->tradedAssets);
        sort($this->tradedAssets);

        $results = [];

        foreach ($this->tradedAssets as $baseAsset) {
            $pairs = $this->tradableAssets[$baseAsset]['pairs'] ?? [];
            if (empty($pairs)) {
                $this->logger->warning('There is no tradable asset pair for ' . $baseAsset);
                continue;
            }

            foreach ($pairs as $symbol) {
                $trades = $this->tradesInfoQuery->setSymbol($symbol)->execute();
                foreach ($trades as $trade) {
                    $results[$trade['orderId']] = $trade;
                }
            }
        }

        return $results;
    }
}
