<?php namespace Mayconbordin\L5Fixtures\Exceptions;


class UnsupportedFormatException extends FixturesException
{
    /**
     * Construct the exception.
     *
     * @param string $format The unsupported format.
     */
    public function __construct($format)
    {
        parent::__construct("Format '$format' is not supported. Supported formats are: JSON, CSV, YAML and PHP.");
    }
}