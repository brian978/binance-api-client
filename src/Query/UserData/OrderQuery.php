<?php /** @noinspection PhpPropertyOnlyWrittenInspection */

declare(strict_types=1);

namespace BinanceApi\Query\UserData;

use BinanceApi\Query\Component\PrivateQuery;
use BinanceApi\Query\Query;

class OrderQuery extends Query
{
    use PrivateQuery;

    protected string $method = 'GET';
    protected string $resource = '/api/v3/order';
    protected int $weight = 2;

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
     * @var string
     */
    protected string $origClientOrderId;

    public function setSymbol(string $symbol): self
    {
        $this->symbol = $symbol;
        return $this;
    }

    public function setOrderId(int $orderId): self
    {
        $this->orderId = $orderId;
        return $this;
    }

    public function setOrigClientOrderId(string $origClientOrderId): self
    {
        $this->origClientOrderId = $origClientOrderId;
        return $this;
    }
}
