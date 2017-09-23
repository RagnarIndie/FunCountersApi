<?php

namespace Test\Counters\Model;


use Test\Counters\Library\PDOModel;

class EventCounters extends PDOModel
{

    /**
     * @var integer
     */
    public $event_id;

    /**
     * @var string
     */
    public $date;

    /**
     * @var string
     */
    public $country;

    /**
     * @var integer
     */
    public $counter;

    /**
     * @param int $value
     */
    public function setEventId(int $value)
    {
        $this->event_id = $value;
    }

    /**
     * @return int
     */
    public function getEventId(): int
    {
        return $this->event_id;
    }

    /**
     * @param string $value
     */
    public function setDate(string $value)
    {
        $this->date = $value;
    }

    /**
     * @return string
     */
    public function getDate(): string
    {
        return $this->date;
    }

    /**
     * @param string $country
     */
    public function setCountry(string $country)
    {
        $this->country = $country;
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @param int $value
     */
    public function setCounter(int $value)
    {
        $this->counter = $value;
    }

    /**
     * @return int
     */
    public function getCounter(): int
    {
        return $this->counter;
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return "event_counters";
    }

    /**
     * Gets the sum of each event over the last 7 days
     * by country for the top 5 countries of all times
     */
    public function getEventCountersForTopCountries()
    {
        $result = [];

        $dateTo = date($this->application->config['db']['date_format'], time());
        $dateFrom = date($this->application->config['db']['date_format'], strtotime("-7 day"));

        //Get top 5 countries. Top is calculates
        //by event counters SUM for a country
        $queryCountries = "SELECT `country`, SUM(`counter`) AS `counter_sum` 
                    FROM `{$this->getTableName()}`
                    GROUP BY `country` 
                    ORDER BY `counter_sum` DESC 
                    LIMIT 5";
        $topCountries = $this->columnAll($queryCountries);

        //If top countries are present we can get events
        //and their counters over the last 7 days
        if (count($topCountries)) {
            $countriesCondition = join("','", $topCountries);

            $eventsQuery = "SELECT 
                              `date`, 
                              `event_title`, 
                              `country`, 
                              `counter` 
                            FROM `{$this->getTableName()}` 
                            WHERE `country` IN ('{$countriesCondition}') 
                              AND `date` BETWEEN '{$dateFrom}' AND '{$dateTo}' 
                            ORDER BY `counter` DESC";
            $result = $this->query($eventsQuery);
        }

        return $result;
    }
}