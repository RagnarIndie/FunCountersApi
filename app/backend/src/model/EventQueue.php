<?php

namespace Test\Counters\Model;


use Test\Counters\Library\PDOModel;

class EventQueue extends PDOModel
{

    /**
     * @var string
     */
    protected $date;

    /**
     * @var string
     */
    protected $eventTitle;

    /**
     * @var string
     */
    protected $country;

    /**
     * @var int
     */
    protected $counter;

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
     * @param string $value
     */
    public function setEventTitle(string $value)
    {
        $this->eventTitle = $value;
    }

    /**
     * @return string
     */
    public function getEventTitle(): string
    {
        return $this->eventTitle;
    }

    /**
     * @param string $value
     */
    public function setCountry(string $value)
    {
        $this->country = $value;
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
        if ($value == 1 || $value == 0) {
            $this->counter = $value;
        }
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
        return 'event_queue';
    }

    public function __construct($application)
    {
        parent::__construct($application);

        //Set date field auto value
        $this->setDate(date($this->application->config['db']['date_format'], time()));
    }
}