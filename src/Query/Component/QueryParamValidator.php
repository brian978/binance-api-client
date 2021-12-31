<?php

namespace BinanceApi\Query\Component;

trait QueryParamValidator
{
    private static function p(?string $paramName): string
    {
        return $paramName ?? 'Parameter';
    }

    protected static function assertParamInArray($needle, array $haystack, ?string $paramName = null): void
    {
        if (!in_array($needle, $haystack)) {
            throw new \InvalidArgumentException(static::p($paramName) . " can only be: " . implode(', ', $haystack));
        }
    }

    private static function assertInBetween(int $needle, int $min, int $max, ?string $paramName = null): void
    {
        if ($needle < $min || $needle > $max) {
            throw new \InvalidArgumentException(static::p($paramName) . " needs to be between $min and $max");
        }
    }

    private static function assertDateIntervalNotExceeded(int $startTime, int $endTime, \DateInterval $interval): void
    {
        $start = new \DateTime();
        $start->setTimestamp($startTime / 1000);

        $end = new \DateTime();
        $end->setTimestamp($endTime / 1000);
        $end->sub($interval); // This takes into account months with missing days, like February

        if ($end->getTimestamp() !== $start->getTimestamp()) {
            throw new \InvalidArgumentException('Date range exceeds ' . $interval->format('%d days'));
        }
    }
}
