<?php

use BinanceApi\ClientBuilder;
use BinanceApi\Component\AccountData;
use BinanceApi\Query\Exception\ResponseException;
use BinanceApi\Query\MarketData\ExchangeInfoQuery;
use BinanceApi\Query\MarketData\TradableAssetQuery;
use BinanceApi\Query\UserData\AccountInformationQuery;
use BinanceApi\Query\UserData\AccountSnapshotQuery;
use BinanceApi\Query\UserData\TradesHistoryQuery;
use BinanceApi\Query\UserData\TradesInfoQuery;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

require_once 'vendor/autoload.php';

// create a default logger
$logger = new Logger('name');
$logger->pushHandler(new StreamHandler('php://stdout', Logger::DEBUG));

/**
 * The file structure MUST be
 *
 * <?php
 *
 * return [
 *     '<your_key>', // KEY
 *     '<your_secret>' // SECRET
 * ];
 */
list($key, $secret) = require __DIR__ . '/config.php';

// Create the client
$clientBuilder = new ClientBuilder();
$clientBuilder
    ->setApiKey($key)
    ->setApiSecret($secret)
    ->setLogger($logger);

// TODO: rate limit object (and therefore the client) needs to be different for each query
// as some queries use the IP limit others the UID

$tradableAssets = [];
$tradableAssetQuery = new TradableAssetQuery(new ExchangeInfoQuery($clientBuilder->build()));
foreach ($tradableAssetQuery->execute() as $asset) {
    if (!isset($tradableAssets[$asset['baseAsset']])) {
        $tradableAssets[$asset['baseAsset']] = ['pairs' => []];
    }

    $tradableAssets[$asset['baseAsset']]['pairs'][] = $asset['symbol'];
}

$accountData = new AccountData(
    new AccountSnapshotQuery($clientBuilder->build()),
    new AccountInformationQuery($clientBuilder->build())
);

// Query the API
$query = new TradesHistoryQuery(
    new TradesInfoQuery($clientBuilder->build())
);

$query->setLogger($logger);
$query->setTradableAssets($tradableAssets);
$query->setTradedAssets($accountData->getTradedAssets());

// Usage
try {
    file_put_contents('trade_history.txt', print_r($query->execute(), 1));
} catch (ResponseException $e) {
    // Handle errors
    print_r($e->getErrors());
}
