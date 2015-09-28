<?php namespace Mayconbordin\L5Fixtures\Loaders;

use League\Csv\Reader;

class CsvLoader extends AbstractLoader
{
    public function load($path)
    {
        $data = $this->metadata->getFilesystem()->read($path);
        return $this->getReader($data)->fetchAssoc();
    }

    /**
     * @param string $data
     * @return static
     */
    protected function getReader($data)
    {
        $csv = Reader::createFromString($data);
        $delimiters = $csv->detectDelimiterList(10, ['|']);

        if (sizeof($delimiters) > 0) {
            $csv->setDelimiter(array_values($delimiters)[0]);
        }

        return $csv;
    }
}