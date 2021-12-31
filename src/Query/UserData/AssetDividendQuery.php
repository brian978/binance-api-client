<?php /** @noinspection PhpPropertyOnlyWrittenInspection */

declare(strict_types=1);

namespace BinanceApi\Query\UserData;

use BinanceApi\Query\Component\PrivateQuery;
use BinanceApi\Query\Query;

class AssetDividendQuery extends Query
{
    use PrivateQuery;

    // Endpoint properties
    protected string $method = 'GET';
    protected string $resource = '/sapi/v1/asset/assetDividend';
    protected int $weight = 10;

    /**
     * @var string
     */
    protected string $asset;

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
     * Default: 100
     * Max: 500
     *
     * @var int
     */
    protected int $limit = 500;

    public function setAsset(string $asset): self
    {
        $this->asset = $asset;
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
        if ($limit > 500) {
            throw new \InvalidArgumentException('Cannot have a limit larger than 500');
        }

        $this->limit = $limit;
        return $this;
    }
}
