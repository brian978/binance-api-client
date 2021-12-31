<?php /** @noinspection PhpPropertyOnlyWrittenInspection */

declare(strict_types=1);

namespace BinanceApi\Query\UserData;

use BinanceApi\Query\Component\PrivateQuery;
use BinanceApi\Query\Component\QueryParamValidator;
use BinanceApi\Query\Query;

class FiatDepositHistoryQuery extends Query
{
    use PrivateQuery, QueryParamValidator;

    protected string $method = 'GET';
    protected string $resource = '/sapi/v1/fiat/orders';
    protected int $weight = 1;

    protected array $required = ['transactionType'];

    public const TYPE_DEPOSIT = '0';
    public const TYPE_WITHDRAW = '1';

    public const STATUS_PENDING = 0;
    public const STATUS_CREDITED = 6;
    public const STATUS_SUCCESS = 1;

    private static array $typeList = [
        self::TYPE_DEPOSIT,
        self::TYPE_WITHDRAW
    ];

    private static array $statusList = [
        self::STATUS_PENDING,
        self::STATUS_CREDITED,
        self::STATUS_SUCCESS
    ];

    /**
     * Whether or not to include trades related to position in output
     *
     * @var string
     */
    protected string $transactionType = self::TYPE_DEPOSIT;

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
    protected int $beginTime;

    /**
     * UNIX timestamp for report end time
     *
     * Default: present timestamp
     *
     * @var int
     */
    protected int $endTime;

    /**
     * Page for the transactions
     *
     * @var int
     */
    protected int $page = 1;

    /**
     * How many results to get
     *
     * Default: 100
     * Max: 500
     *
     * @var int
     */
    protected int $rows = 100;

    public function setTransactionType(string $transactionType): self
    {
        static::assertParamInArray($transactionType, self::$typeList, 'Transaction type');

        $this->transactionType = $transactionType;
        return $this;
    }

    public function setStatus(int $status): self
    {
        static::assertParamInArray($status, self::$statusList, 'Status');

        $this->status = $status;
        return $this;
    }

    public function setBeginTime(int $beginTime): self
    {
        $this->beginTime = $beginTime;
        return $this;
    }

    public function setEndTime(int $endTime): self
    {
        $this->endTime = $endTime;
        return $this;
    }

    public function setPage(int $page): self
    {
        $this->page = $page;
        return $this;
    }

    public function nextPage(): void
    {
        $this->page++;
    }

    public function setRows(int $rows): self
    {
        $this->rows = $rows;
        return $this;
    }
}
