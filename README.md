# Binance API client
Unofficial API client written in PHP for Binance Exchange

[Official API documentation](https://binance-docs.github.io/apidocs/spot/en/)

# UNDER DEVELOPMENT

# Installation
```shell
composer require brian978/binance-api-client
```

# Main features
* Compatible with any framework
* Compatible with any logger library that implements `Psr\Log\LoggerInterface`
* It provides an easy-to-use interface with the API using the *Query classes that are provided
* The API parameter documentation can be found in the PHPDoc of each query object for easy use

# How to contribute to the project
If we are talking about a new feature, then an issue needs to be opened in order to discuss it. And after that proceed
to the steps below.

If it's a BUG see the steps below

## Steps to update the code
1. Write the code in a new branch (see [HERE](https://www.atlassian.com/git/tutorials/comparing-workflows/gitflow-workflow) for more info)
2. Make sure it's PSR-12 compliant and all the code and comments ar properly aligned
3. Make a PR

# Configuration examples
## Symfony

In your .env config file
```text
###> brian978/binance-api-client ###
BINANCE_API_KEY=<your_key>
BINANCE_API_SECRET=<your_secret>==
###< brian978/binance-api-client ###
```

In your services.yaml
```yaml
# BinanceApi namespace auto-wire
BinanceApi\:
  resource: '../vendor/brian978/binance-api-client/src/'

# Binance API rate limiter
app.limiter.storage.binance_api:
  class: Symfony\Component\RateLimiter\Storage\CacheStorage

BinanceApi\RateLimiter\RateLimiter:
  shared: false
  arguments:
    - '@app.limiter.storage.binance_api'
    - '@lock.default.factory'

# Binance API client
app.binanceHttp.client:
  class: GuzzleHttp\Client
  arguments:
    - { base_uri: 'https://api.binance.com' }

BinanceApi\Client:
  shared: false
  class: BinanceApi\Client
  arguments:
    - '@app.binanceHttp.client'
    - '%env(resolve:BINANCE_API_KEY)%'
    - '%env(resolve:BINANCE_API_SECRET)%'
    - '@BinanceApi\RateLimiter\RateLimiter'
  calls:
    - setLogger: [ '@logger' ]
```
