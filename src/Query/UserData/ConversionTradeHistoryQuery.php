<?php
/** @noinspection PhpPropertyOnlyWrittenInspection */

declare(strict_types=1);

namespace BinanceApi\Query\UserData;

use BinanceApi\Query\Component\DatePagedQuery;
use BinanceApi\Query\Component\PrivateQuery;
use BinanceApi\Query\Component\QueryParamValidator;
use BinanceApi\Query\Query;

class ConversionTradeHistoryQuery extends Query
{
    use PrivateQuery, DatePagedQuery, QueryParamValidator;

    // Endpoint properties
    protected string $method = 'GET';
    protected string $resource = '/sapi/v1/convert/tradeFlow';
    protected int $weight = 100;

    // Request properties
    protected array $required = ['startTime', 'endTime'];

    /**
     * How many results to get
     *
     * Default: 100
     * Max: 1000
     *
     * @var int
     */
    protected int $limit = 100;

    public function setLimit(int $limit): self
    {
        static::assertInBetween($limit, 100, 1000, 'Limit');

        $this->limit = $limit;
        return $this;
    }

    public function execute(): array
    {
        $result = [];

        foreach ($this->pagedExecute() as $data) {
            foreach ($data['list'] ?? [] as $item) {
                $result[] = $item;
            }
        }

        return array_reverse($result);
    }
}
