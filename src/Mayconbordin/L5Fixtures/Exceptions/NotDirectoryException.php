<?php namespace Mayconbordin\L5Fixtures\Exceptions;


use Exception;

class NotDirectoryException extends FixturesException
{
    /**
     * Construct the exception.
     *
     * @param string $location The location that is not a directory.
     */
    public function __construct($location)
    {
        parent::__construct("Fixtures location '$location' is not a directory or can't be read.");
    }

}