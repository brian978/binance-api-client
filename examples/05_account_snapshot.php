<?php

use BinanceApi\ClientBuilder;
use BinanceApi\Query\UserData\AccountSnapshotQuery;
use BinanceApi\Query\UserData\ConversionTradeHistoryQuery;
use GuzzleHttp\Exception\GuzzleException;

require_once 'vendor/autoload.php';

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
    ->setApiSecret($secret);

$client = $clientBuilder->build();

// Query the API
$query = new AccountSnapshotQuery($client);
$query->setEndDate(new DateTime(), new DateInterval('P30D'));
$query->setLookBackPeriod(6);

// Usage
try {
    print_r($query->execute());
} catch (GuzzleException $e) {
    echo $e->getMessage();
    echo $e->getTraceAsString();
}

