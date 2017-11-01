<?php namespace Mayconbordin\L5Fixtures\Commands;

use Illuminate\Console\Command;
use Mayconbordin\L5Fixtures\Exceptions\FixturesException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

use \DB;
use \Fixtures;

class UpCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'fixtures:up {--fixtures=} {--dir=} {--database=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Apply all fixtures to the database';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $database = $this->option('database');
        $dir      = $this->option('dir');
        $fixtures = $this->option('fixtures');

        if ($database != null) {
            DB::setDefaultConnection($database);
        }

        if ($dir != null) {
            Fixtures::setUp($dir);
        }

        if ($fixtures == null) {
            Fixtures::up();
        } else if (is_array($fixtures)) {
            forward_static_call_array(array('Fixtures', 'up'), $fixtures);
        } else {
            throw new FixturesException('List of fixtures should be an array.');
        }
    }
}