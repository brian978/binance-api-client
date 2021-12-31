<?php

declare(strict_types=1);

namespace BinanceApi\Query\UserData;

use BinanceApi\Query\Component\DatePagedQuery;
use BinanceApi\Query\Component\PrivateQuery;
use BinanceApi\Query\Component\QueryParamValidator;
use BinanceApi\Query\Query;

/**
 * The look back period for this query can only be 6 months (6 x 30days)
 */
class AccountSnapshotQuery extends Query
{
    use PrivateQuery, DatePagedQuery, QueryParamValidator;

    protected string $method = 'GET';
    protected string $resource = '/sapi/v1/accountSnapshot';
    protected int $weight = 2400;

    public const TYPE_SPOT = 'SPOT';
    public const TYPE_MARGIN = 'MARGIN';
    public const TYPE_FUTURES = 'FUTURES';

    private static array $typeList = [
        self::TYPE_SPOT,
        self::TYPE_MARGIN,
        self::TYPE_FUTURES
    ];

    /**
     * @var string
     */
    protected string $type = 'SPOT';

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
     * How many results to get
     *
     * Min: 5 (default)
     * Max: 30
     *
     * @var int
     */
    protected int $limit = 30;

    public function setType(string $type): self
    {
        if (!in_array($type, self::$typeList)) {
            throw new \InvalidArgumentException('Invalid type value');
        }

        $this->type = $type;
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

    public function setLimit(int $limit): self
    {
        static::assertInBetween($limit, 5, 30, 'Limit');

        $this->limit = $limit;
        return $this;
    }

    public function execute(): array
    {
        $this->setEndDate(new \DateTime());

        $result = [];

        foreach ($this->pagedExecute() as $data) {
            foreach ($data ?? [] as $item) {
                $result[] = $item;
            }
        }

        return array_reverse($result);
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getTradedAssets(float $minThreshold = 0): array
    {
        $tradesAssets = [];

        $snapshot = $this->execute();
        $dailySnapshot = $snapshot['snapshotVos'] ?? [];
        foreach ($dailySnapshot as $snapshot) {
            foreach ($snapshot['data']['balances'] ?? [] as $balance) {
                if ((float)$balance['free'] >= $minThreshold || (float)$balance['locked'] >= $minThreshold) {
                    $tradesAssets[] = $balance['asset'];
                }
            }
        }

        $tradesAssets = array_unique($tradesAssets);
        sort($tradesAssets);

        return $tradesAssets;
    }
}
