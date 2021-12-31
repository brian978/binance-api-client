<?php /** @noinspection PhpPropertyOnlyWrittenInspection */

declare(strict_types=1);

namespace BinanceApi\Query\UserData;

use BinanceApi\Query\Component\PrivateQuery;
use BinanceApi\Query\Query;

class FundingQuery extends Query
{
    use PrivateQuery;

    protected string $method = 'POST';
    protected string $resource = '/sapi/v1/asset/get-funding-asset';
    protected int $weight = 1;

    private static array $assetList = [];

    /**
     * Target asset
     *
     * Supports querying the following business assets：Binance Pay, Binance Card, Binance Gift Card, Stock Token
     *
     * @var string
     */
    protected string $asset;

    /**
     * @var string
     */
    protected string $needBtcValuation;

    public function setAsset(string $asset): FundingQuery
    {
        $this->asset = $asset;
        return $this;
    }

    public function setNeedBtcValuation(string $needBtcValuation): FundingQuery
    {
        $this->needBtcValuation = $needBtcValuation;
        return $this;
    }
}
