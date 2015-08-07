<?php namespace Mayconbordin\L5Fixtures\Exceptions;

use Exception;

class FixturesException extends \Exception
{
    /**
     * Construct the exception.
     *
     * @param string $message [optional] The Exception message to throw.
     * @param int $code [optional] The Exception code.
     * @param Exception $previous [optional] The previous exception used for the exception chaining.
     */
    public function __construct($message = "", $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}