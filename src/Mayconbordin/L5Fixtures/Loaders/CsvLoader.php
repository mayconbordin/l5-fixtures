<?php namespace Mayconbordin\L5Fixtures\Loaders;

use League\Csv\Reader;

class CsvLoader extends AbstractLoader
{
    public function load($path)
    {
        $data = $this->metadata->getFilesystem()->read($path);
        return iterator_to_array($this->getReader($data)->fetchAssoc(), false);
    }

    /**
     * @param string $data
     * @return Reader
     */
    protected function getReader($data)
    {
        $csv = Reader::createFromString($data);
        $delimiters = $csv->fetchDelimitersOccurrence([' ', '|', ',', ';'], 10);

        if (sizeof($delimiters) > 0) {
            $csv->setDelimiter(array_keys($delimiters)[0]);
        }

        return $csv;
    }
}