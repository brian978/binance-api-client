<?php /** @noinspection PhpPropertyOnlyWrittenInspection */

declare(strict_types=1);

namespace BinanceApi\Query\MarketData;

use BinanceApi\Query\Component\PublicQuery;
use BinanceApi\Query\Query;

class ExchangeInfoQuery extends Query
{
    use PublicQuery;

    protected string $method = 'GET';
    protected string $resource = '/api/v3/exchangeInfo';
    protected int $weight = 10;

    /**
     * Query by one symbol
     *
     * Example: BNBBTC
     *
     * @var string
     */
    protected string $symbol;

    /**
     * Query by multiple symbols
     *
     * Example: ["BTCUSDT","BNBBTC"]
     *
     * @var string
     */
    protected string $symbols;

    public function setSymbol(string $symbol): ExchangeInfoQuery
    {
        $this->symbol = $symbol;

        unset($this->symbols); // Cannot query with both

        return $this;
    }

    public function setSymbols(array $symbols): ExchangeInfoQuery
    {
        $this->symbols = json_encode($symbols);

        unset($this->symbol); // Cannot query with both

        return $this;
    }
}
