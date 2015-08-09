<?php

use Illuminate\Support\Facades\DB;
use Mockery as m;

class FixturesTest extends PHPUnit_Framework_TestCase
{
    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        parent::tearDown();

        m::close();
    }


    public function testUp()
    {
        $queryBuilder = m::mock('Illuminate\Database\Query\Builder');
        $queryBuilder->shouldReceive('insert')->with(m::type('array'))->times(4);

        DB::shouldReceive('statement')->with('SET FOREIGN_KEY_CHECKS=0;')->once();
        DB::shouldReceive('statement')->with('SET FOREIGN_KEY_CHECKS=1;')->once();

        DB::shouldReceive('table')->with('cities')->once()->andReturn($queryBuilder);
        DB::shouldReceive('table')->with('users')->once()->andReturn($queryBuilder);
        DB::shouldReceive('table')->with('countries')->once()->andReturn($queryBuilder);
        DB::shouldReceive('table')->with('logs')->once()->andReturn($queryBuilder);

        $fixtures = new \Mayconbordin\L5Fixtures\Fixtures(['location' => __DIR__ . '/_data']);

        $fixtures->up();

        $this->assertEquals(4, sizeof($fixtures->getFixtures()));
    }

    public function testUpOnlyUsers()
    {
        $queryBuilder = m::mock('Illuminate\Database\Query\Builder');
        $queryBuilder->shouldReceive('insert')->with(m::type('array'))->once();

        DB::shouldReceive('statement')->with('SET FOREIGN_KEY_CHECKS=0;')->once();
        DB::shouldReceive('statement')->with('SET FOREIGN_KEY_CHECKS=1;')->once();
        DB::shouldReceive('table')->with('users')->once()->andReturn($queryBuilder);

        $fixtures = new \Mayconbordin\L5Fixtures\Fixtures(['location' => __DIR__ . '/_data']);

        $fixtures->up(['users']);

        $this->assertEquals(4, sizeof($fixtures->getFixtures()));
    }

    public function testDown()
    {
        $queryBuilder = m::mock('Illuminate\Database\Query\Builder');
        $queryBuilder->shouldReceive('truncate')->times(4);

        DB::shouldReceive('statement')->with('SET FOREIGN_KEY_CHECKS=0;')->once();
        DB::shouldReceive('statement')->with('SET FOREIGN_KEY_CHECKS=1;')->once();

        DB::shouldReceive('table')->with('cities')->once()->andReturn($queryBuilder);
        DB::shouldReceive('table')->with('users')->once()->andReturn($queryBuilder);
        DB::shouldReceive('table')->with('countries')->once()->andReturn($queryBuilder);
        DB::shouldReceive('table')->with('logs')->once()->andReturn($queryBuilder);

        $fixtures = new \Mayconbordin\L5Fixtures\Fixtures(['location' => __DIR__ . '/_data']);

        $fixtures->down();

        $this->assertEquals(4, sizeof($fixtures->getFixtures()));
    }

    public function testGetFixtures()
    {
        $fixtures = new \Mayconbordin\L5Fixtures\Fixtures(['location' => __DIR__ . '/_data']);
        $this->assertEquals(4, sizeof($fixtures->getFixtures()));
    }
}