<?php namespace Mayconbordin\L5Fixtures;

use Illuminate\Support\Facades\Facade;

class FixturesFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'fixtures';
    }
}