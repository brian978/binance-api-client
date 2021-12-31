<?php

namespace BinanceApi\Query\Component;

trait DatePagedQuery
{
    /*
     * How many intervals should it look back
     *
     * Example:
     * $query = new Query($client);
     * $query->setEndDate(new DateTime(), new DateInterval('P30D'));
     * $query->setLookBackPeriod(24);
     *
     * Will look back starting from today with a look back interval of 30 days (which is max).
     * The look back will be done for 24 intervals (meaning 24 * 30days starting from today)
     *
     * @var int
     */
    private int $lookBackPeriod = 1;

    /**
     * UNIX timestamp for report start time
     *
     * @var int
     */
    protected int $startTime;

    /**
     * UNIX timestamp for report end time
     *
     * @var int
     */
    protected int $endTime;

    public function setLookBackPeriod(int $lookBackPeriod): self
    {
        $this->lookBackPeriod = $lookBackPeriod;

        return $this;
    }

    public function setEndDate(\DateTimeInterface $endDate, ?\DateInterval $lookBackInterval = null): self
    {
        $lookBackInterval = $lookBackInterval ?? new \DateInterval('P30D');

        $startDate = clone $endDate;
        $startDate->sub($lookBackInterval);

        $this->endTime = $endDate->getTimestamp() * 1000;
        $this->startTime = $startDate->getTimestamp() * 1000;

        return $this;
    }

    private function goBack(): bool
    {
        $startTime = $this->startTime / 1000;
        $endDate = $this->endTime / 1000;
        $dateInterval = $endDate - $startTime;

        $startTime -= 1; // -1 because no need to overlap intervals

        // Set the end time to the start time of the previous query since we are looking back
        $this->endTime = $startTime * 1000;
        $this->startTime = ($startTime - $dateInterval) * 1000;

        return true;
    }

    protected function preExecuteSetup(\DateInterval $interval): void
    {
        if (!isset($this->endTime)) {
            $this->setEndDate(new \DateTime(), $interval);
        }

        static::assertDateIntervalNotExceeded($this->startTime, $this->endTime, $interval);
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function pagedExecute(): \Generator
    {
        for ($i = 1; $i <= $this->lookBackPeriod; $i++) {
            yield parent::execute();
            $this->goBack();
        }
    }
}
