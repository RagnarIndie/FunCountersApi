<?php

namespace Test\Counters\Library\Formatters;

use Test\Counters\Library\Interfaces\IFormatter;

abstract class AbstractFormatter implements IFormatter
{
    /**
     * MIME type for a current response formatter
     * @var string
     */
    protected $mimeType = '';

    /**
     * Returns proper Content-Type header
     *
     * @return bool|string
     */
    public function getContentTypeHeader()
    {
        if (!empty($this->mimeType)) {
            return "Content-Type: {$this->mimeType}";
        }

        return false;
    }
}