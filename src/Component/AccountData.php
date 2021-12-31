<?php

namespace BinanceApi\Component;

use BinanceApi\Query\UserData\AccountInformationQuery;
use BinanceApi\Query\UserData\AccountSnapshotQuery;

class AccountData
{
    private AccountSnapshotQuery $accountSnapshotQuery;
    private AccountInformationQuery $accountInformationQuery;

    public function __construct(
        AccountSnapshotQuery $accountSnapshotQuery,
        AccountInformationQuery $accountInformationQuery
    ) {
        $this->accountSnapshotQuery = $accountSnapshotQuery;
        $this->accountInformationQuery = $accountInformationQuery;
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getTradedAssets(float $minThreshold = 0): array
    {
        return array_merge(
            $this->accountSnapshotQuery->getTradedAssets($minThreshold),
            $this->accountInformationQuery->getTradedAssets($minThreshold)
        );
    }
}
