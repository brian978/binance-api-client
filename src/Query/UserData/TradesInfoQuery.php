<?php /** @noinspection PhpPropertyOnlyWrittenInspection */

declare(strict_types=1);

namespace BinanceApi\Query\UserData;

use BinanceApi\Query\Component\PrivateQuery;
use BinanceApi\Query\Component\QueryParamValidator;
use BinanceApi\Query\Query;

class TradesInfoQuery extends Query
{
    use PrivateQuery, QueryParamValidator;

    protected string $method = 'GET';
    protected string $resource = '/api/v3/myTrades';
    protected int $weight = 10;

    protected array $required = ['symbol'];

    /**
     * Whether or not to include trades related to position in output
     *
     * @var string
     */
    protected string $symbol;

    /**
     * This can only be used in combination with symbol.
     *
     * @var int
     */
    protected int $orderId;

    /**
     * UNIX timestamp for report start time
     *
     * @var int
     */
    protected int $startTime;

    /**
     * UNIX timestamp for report end time
     *
     * @var int
     */
    protected int $endTime;

    /**
     * TradeId to fetch from. Default gets most recent trades.
     *
     * @var int
     */
    protected int $fromId;

    /**
     * How many results to get
     *
     * Default: 500
     * Max: 1000
     *
     * @var int
     */
    protected int $limit = 500;

    public function setSymbol(string $symbol): TradesInfoQuery
    {
        $this->symbol = $symbol;
        return $this;
    }

    public function setOrderId(int $orderId): TradesInfoQuery
    {
        $this->orderId = $orderId;
        return $this;
    }

    public function setStartTime(int $startTime): TradesInfoQuery
    {
        $this->startTime = $startTime;
        return $this;
    }

    public function setEndTime(int $endTime): TradesInfoQuery
    {
        $this->endTime = $endTime;
        return $this;
    }

    public function setFromId(int $fromId): TradesInfoQuery
    {
        $this->fromId = $fromId;
        return $this;
    }

    public function setLimit(int $limit): TradesInfoQuery
    {
        static::assertInBetween($limit, 0, 1000, 'Limit');

        $this->limit = $limit;
        return $this;
    }
}
