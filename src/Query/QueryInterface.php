<?php

declare(strict_types=1);

namespace BinanceApi\Query;

interface QueryInterface
{
    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function execute(): array;
}
