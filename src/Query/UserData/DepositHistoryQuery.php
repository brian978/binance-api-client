<?php /** @noinspection PhpPropertyOnlyWrittenInspection */

declare(strict_types=1);

namespace BinanceApi\Query\UserData;

use BinanceApi\Query\Component\PrivateQuery;
use BinanceApi\Query\Component\QueryParamValidator;
use BinanceApi\Query\Query;

class DepositHistoryQuery extends Query
{
    use PrivateQuery, QueryParamValidator;

    protected string $method = 'GET';
    protected string $resource = '/sapi/v1/capital/deposit/hisrec';
    protected int $weight = 1;

    public const STATUS_PENDING = 0;
    public const STATUS_CREDITED = 6;
    public const STATUS_SUCCESS = 1;

    private static array $statusList = [
        self::STATUS_PENDING,
        self::STATUS_CREDITED,
        self::STATUS_SUCCESS,
    ];

    /**
     * Whether or not to include trades related to position in output
     *
     * @var string
     */
    protected string $coin;

    /**
     * Status of the deposit
     *
     * 0: pending
     * 6: credited but cannot withdraw
     * 1: success
     *
     * @var int
     */
    protected int $status;

    /**
     * UNIX timestamp for report start time
     *
     * Default: 90 days from current timestamp
     *
     * @var int
     */
    protected int $startTime;

    /**
     * UNIX timestamp for report end time
     *
     * Default: present timestamp
     *
     * @var int
     */
    protected int $endTime;

    /**
     * Offset to fetch from
     *
     * @var int
     */
    protected int $offset;

    /**
     * How many results to get
     *
     * Default: 1000
     * Max: 1000
     *
     * @var int
     */
    protected int $limit = 1000;

    public function setCoin(string $coin): self
    {
        $this->coin = $coin;
        return $this;
    }

    public function setStatus(int $status): self
    {
        static::assertParamInArray($status, static::$statusList, 'Status');

        $this->status = $status;
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

    public function setOffset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    public function setLimit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }
}
