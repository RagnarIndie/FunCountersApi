<?php

namespace Test\Counters\Library\Interfaces;


interface IResponse
{
    public function setFormatter(string $format);

    /**
     * @return \Test\Counters\Library\Interfaces\IFormatter
     */
    public function getFormatter();
    public function setResponseHeaderByCode(int $code);
}