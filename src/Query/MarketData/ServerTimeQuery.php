<?php /** @noinspection PhpPropertyOnlyWrittenInspection */

declare(strict_types=1);

namespace BinanceApi\Query\MarketData;

use BinanceApi\Query\Component\PublicQuery;
use BinanceApi\Query\Query;

class ServerTimeQuery extends Query
{
    use PublicQuery;

    protected string $method = 'GET';
    protected string $resource = '/api/v3/time';
    protected int $weight = 1;
}
