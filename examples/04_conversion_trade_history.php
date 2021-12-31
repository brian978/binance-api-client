<?php

use BinanceApi\ClientBuilder;
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
$query = new ConversionTradeHistoryQuery($client);
$query->setEndDate(new DateTime(), new DateInterval('P30D'));
$query->setLookBackPeriod(24);

// Usage
try {
    print_r($query->execute());
} catch (GuzzleException $e) {
    // Handle errors
    print_r($e->getErrors());
}

