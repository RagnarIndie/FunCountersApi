<?php

namespace Test\Counters\Library\Formatters;


class CsvFormatter extends AbstractFormatter
{
    protected $mimeType = 'text/csv';

    /**
     * Returns data in JSON format
     *
     * @param array $data
     * @return string
     */
    public function formatResponse(array $data)
    {
        return $this->putCsv(array_values($data));
    }

    protected function putCsv($data)
    {
        # Generate CSV data from array
        $fh = fopen('php://temp', 'rw'); # don't create a file, attempt
        # to use memory instead

        # write out the headers
        fputcsv($fh, array_keys(current($data)));

        # write out the data
        foreach ( $data as $row ) {
            fputcsv($fh, $row);
        }

        rewind($fh);
        $csv = stream_get_contents($fh);
        fclose($fh);

        return $csv;
    }
}