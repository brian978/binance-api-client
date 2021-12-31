<?php
/** @noinspection PhpPropertyOnlyWrittenInspection */

declare(strict_types=1);

namespace BinanceApi\Query\UserData;

use BinanceApi\Query\Component\DatePagedQuery;
use BinanceApi\Query\Component\PrivateQuery;
use BinanceApi\Query\Component\QueryParamValidator;
use BinanceApi\Query\Query;

/**
 * By default, it retrieves the data from the last 30 days
 *
 * Set the archive flag if the query is for more than 60 days
 */
class CrossMarginTransferHistoryQuery extends Query
{
    use PrivateQuery, DatePagedQuery, QueryParamValidator;

    // Endpoint properties
    protected string $method = 'GET';
    protected string $resource = '/sapi/v1/margin/transfer';
    protected int $weight = 1;

    public const TYPE_ROLL_IN = 'ROLL_IN';
    public const TYPE_ROLL_OUT = 'ROLL_OUT';

    private static array $types = [
        self::TYPE_ROLL_IN,
        self::TYPE_ROLL_OUT,
    ];

    /**
     * Comma delimited list of assets to restrict output to
     *
     * @var string
     */
    protected string $asset;

    /**
     * Type of ledger to retrieve
     *
     * @var string
     */
    protected string $type;

    /**
     * Starting unix timestamp
     *
     * @var int
     */
    protected int $startTime;

    /**
     * Ending unix timestamp
     *
     * @var int
     */
    protected int $endTime;

    /**
     * Currently querying page. Start from 1. Default:1
     *
     * @var int
     */
    protected int $current = 1;

    /**
     * Default:10 Max:100
     *
     * @var int
     */
    protected int $size = 10;

    /**
     * Default: false. Set to true for archived data from 6 months ago
     *
     * @var string
     */
    protected string $archived = 'false';

    public function setAsset(string $asset): self
    {
        $this->asset = $asset;
        return $this;
    }

    public function setType(string $type): self
    {
        static::assertParamInArray($type, static::$types, 'Type');

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

    public function setCurrent(int $current): self
    {
        $this->current = $current;
        return $this;
    }

    public function setSize(int $size): self
    {
        static::assertInBetween($size, 10, 100, 'Size');

        $this->size = $size;
        return $this;
    }

    public function setArchived(string $archived): self
    {
        self::assertParamInArray($archived, ['false', 'true'], 'Archived');

        $this->archived = $archived;
        return $this;
    }

    public function execute(): array
    {
        $this->preExecuteSetup(new \DateInterval('P30D'));

        $retrieved = 0;
        $results = [];

        foreach ($this->pagedExecute() as $data) { // Date based pagination
            do {
                $total = (int)$data['total'];

                foreach ($data['rows'] as $row) {
                    $results[] = $row;
                }

                $retrieved += count($data['rows']);

                $this->current++;
                $data = parent::execute(); // Next page of the interval
            } while ($retrieved < $total);
        }

        return $results;
    }
}
