<?php /** @noinspection PhpPropertyOnlyWrittenInspection */

declare(strict_types=1);

namespace BinanceApi\Query\MarketData;

use BinanceApi\Query\QueryInterface;

class TradableAssetQuery implements QueryInterface
{
    private ExchangeInfoQuery $exchangeInfoQuery;

    public function __construct(ExchangeInfoQuery $exchangeInfoQuery)
    {
        $this->exchangeInfoQuery = $exchangeInfoQuery;
    }

    public function setSymbol(string $symbol): TradableAssetQuery
    {
        $this->exchangeInfoQuery->setSymbol($symbol);

        return $this;
    }

    public function setSymbols(array $symbols): TradableAssetQuery
    {
        $this->exchangeInfoQuery->setSymbols($symbols);

        return $this;
    }

    public function execute(): array
    {
        $exchangeInfo = $this->exchangeInfoQuery->execute();

        return $exchangeInfo['symbols'] ?? [];
    }
}
