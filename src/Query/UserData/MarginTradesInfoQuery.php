<?php /** @noinspection PhpPropertyOnlyWrittenInspection */

declare(strict_types=1);

namespace BinanceApi\Query\UserData;

use BinanceApi\Query\Component\DatePagedQuery;
use BinanceApi\Query\Component\PrivateQuery;
use BinanceApi\Query\Component\QueryParamValidator;
use BinanceApi\Query\Query;

class MarginTradesInfoQuery extends Query
{
    use PrivateQuery, DatePagedQuery, QueryParamValidator;

    protected string $method = 'GET';
    protected string $resource = '/sapi/v1/margin/myTrades';
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
     * @var string
     */
    protected string $isIsolated = 'false';

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

    public function setSymbol(string $symbol): self
    {
        $this->symbol = $symbol;
        return $this;
    }

    public function setIsIsolated(string $isIsolated): self
    {
        static::assertParamInArray($isIsolated, ['false', 'true', 'Is isolated']);

        $this->isIsolated = $isIsolated;
        return $this;
    }

    public function setStartTime(int $startTime): self
    {
        $this->startTime = $startTime;
        return $this;
    }

    public function setEndTime(int $endTime): self
    {
        $this->endTime = $endTime;
        return $this;
    }

    public function setFromId(int $fromId): self
    {
        $this->fromId = $fromId;
        return $this;
    }

    public function setLimit(int $limit): self
    {
        static::assertInBetween($limit, 0, 1000, 'Limit');

        $this->limit = $limit;
        return $this;
    }

    public function execute(): array
    {
        $this->preExecuteSetup(new \DateInterval('P1D'));

        $result = [];

        foreach ($this->pagedExecute() as $data) {
            foreach ($data ?? [] as $item) {
                $result[] = $item;
            }
        }

        return array_reverse($result);
    }
}
