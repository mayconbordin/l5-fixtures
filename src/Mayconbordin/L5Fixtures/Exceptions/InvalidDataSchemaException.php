<?php namespace Mayconbordin\L5Fixtures\Exceptions;


class InvalidDataSchemaException extends FixturesException
{
    /**
     * Construct the exception.
     *
     * @param string $fixture The name of the invalid fixture.
     */
    public function __construct($fixture)
    {
        parent::__construct("The parsed data schema for the $fixture fixture is not valid. It should be a list of records.");
    }
}