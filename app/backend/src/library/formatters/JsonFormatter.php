<?php

namespace Test\Counters\Library\Formatters;


class JsonFormatter extends AbstractFormatter
{
    protected $mimeType = 'application/json';

    /**
     * Returns data in JSON format
     *
     * @param array $data
     * @return string
     */
    public function formatResponse(array $data)
    {
        return json_encode($data);
    }
}