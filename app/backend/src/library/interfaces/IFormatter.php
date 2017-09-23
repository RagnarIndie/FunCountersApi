<?php

namespace Test\Counters\Library\Interfaces;


interface IFormatter
{
    public function getContentTypeHeader();
    public function formatResponse(array $data);
}