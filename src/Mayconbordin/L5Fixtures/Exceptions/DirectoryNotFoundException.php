<?php namespace Mayconbordin\L5Fixtures\Exceptions;


class DirectoryNotFoundException extends FixturesException
{
    /**
     * Construct the exception.
     *
     * @param string $location The location that does not exists.
     */
    public function __construct($location)
    {
        parent::__construct("Fixtures location '$location' does not exists.");
    }
}