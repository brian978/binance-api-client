<?php /** @noinspection PhpPropertyOnlyWrittenInspection */

declare(strict_types=1);

namespace BinanceApi\Query\UserData;

use BinanceApi\Query\Component\PrivateQuery;
use BinanceApi\Query\Query;

class WithdrawHistoryQuery extends Query
{
    use PrivateQuery;

    protected string $method = 'GET';
    protected string $resource = '/sapi/v1/capital/withdraw/history';
    protected int $weight = 1;

    public const STATUS_EMAILED = 0;
    public const STATUS_CANCELED = 1;
    public const STATUS_AWAITING_APPROVAL = 2;
    public const STATUS_REJECTED = 3;
    public const STATUS_PROCESSING = 4;
    public const STATUS_FAILURE = 5;
    public const STATUS_COMPLETED = 6;

    private static array $statusList = [
        self::STATUS_EMAILED,
        self::STATUS_CANCELED,
        self::STATUS_AWAITING_APPROVAL,
        self::STATUS_REJECTED,
        self::STATUS_PROCESSING,
        self::STATUS_FAILURE,
        self::STATUS_COMPLETED
    ];

    /**
     * Whether or not to include trades related to position in output
     *
     * @var string
     */
    protected string $coin;

    /**
     * @var string
     */
    protected string $withdrawOrderId;

    /**
     * Status of the deposit
     *
     * 0: Email sent
     * 1: Canceled
     * 2: Awaiting approval
     * 3: Rejected
     * 4: Processing
     * 5: Failure
     * 6: Completed
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

    public function setWithdrawOrderId(string $withdrawOrderId): self
    {
        $this->withdrawOrderId = $withdrawOrderId;
        return $this;
    }

    public function setStatus(int $status): self
    {
        if (!in_array($status, static::$statusList)) {
            throw new \InvalidArgumentException('Invalid status provided!');
        }

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
