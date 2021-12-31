<?php

declare(strict_types=1);

namespace BinanceApi\Query\UserData;

use BinanceApi\Query\Component\PrivateQuery;
use BinanceApi\Query\Query;

class AccountInformationQuery extends Query
{
    use PrivateQuery;

    protected string $method = 'GET';
    protected string $resource = '/api/v3/account';
    protected int $weight = 10;

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getTradedAssets(float $minThreshold = 0): array
    {
        $tradesAssets = [];

        $accountInformation = $this->execute();
        foreach ($accountInformation['balances'] ?? [] as $balance) {
            if ((float)$balance['free'] >= $minThreshold || (float)$balance['locked'] >= $minThreshold) {
                $tradesAssets[] = $balance['asset'];
            }
        }

        $tradesAssets = array_unique($tradesAssets);
        sort($tradesAssets);

        return $tradesAssets;
    }
}
