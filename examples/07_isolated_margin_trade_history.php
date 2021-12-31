<?php

use BinanceApi\ClientBuilder;
use BinanceApi\Query\UserData\MarginTradesInfoQuery;
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
$query = new MarginTradesInfoQuery($client);
$query->setLookBackPeriod(365); // 1 year - query can only be done 1 day at a time
$query->setEndDate(new DateTime(), new DateInterval('P1D'));
$query->setSymbol('ENJUSDT');
$query->setIsIsolated('true');

// Usage
try {
    print_r($query->execute());
} catch (GuzzleException $e) {
    echo $e->getMessage();
    echo $e->getTraceAsString();
}


