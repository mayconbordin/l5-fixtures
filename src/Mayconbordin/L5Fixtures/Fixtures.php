<?php namespace Mayconbordin\L5Fixtures;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use Mayconbordin\L5Fixtures\Exceptions\DirectoryNotFoundException;
use Mayconbordin\L5Fixtures\Exceptions\NotDirectoryException;
use Mayconbordin\L5Fixtures\Loaders\LoaderFactory;

/**
 * Class Fixtures
 * @package Mayconbordin\L5Fixtures
 */
class Fixtures
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var FixturesMetadata
     */
    protected $metadata;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function setUp($location = null)
    {
        if ($location == null) {
            $location = array_get($this->config, 'location');
        }

        if (!is_dir($location) || !is_readable($location)) {
            throw new NotDirectoryException($location);
        }

        if (!file_exists($location)) {
            throw new DirectoryNotFoundException($location);
        }

        $this->metadata = new FixturesMetadata($location);
    }

    public function up($fixtures = null)
    {
        $this->loadFixtures($fixtures);
    }

    public function down($fixtures = null)
    {
        $this->unloadFixtures($fixtures);
    }

    protected function unloadFixtures($allowed = null)
    {
        $fixtures = $this->getFixtures($allowed);

        Model::unguard();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        foreach ($fixtures as $fixture)
        {
            DB::table($fixture->table)->truncate();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    protected function loadFixtures($allowed = null)
    {
        $fixtures = $this->getFixtures($allowed);

        Model::unguard();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        foreach ($fixtures as $fixture)
        {
            $this->loadFixture($fixture);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    protected function loadFixture($fixture)
    {
        $rows = LoaderFactory::create($fixture->format, $this->metadata)->load($fixture->path);

        if (!is_array($rows)) {
            var_dump($fixture);
            var_dump($rows);
        }

        DB::table($fixture->table)->insert($rows);
    }

    public function getFixtures($allowed = null)
    {
        if ($this->metadata == null) {
            $this->setUp();
        }

        if (is_null($allowed)) {
            $fixtures = $this->metadata->getFixtures();
        } else {
            $fixtures = array_intersect_key($this->metadata->getFixtures(), array_flip($allowed));
        }

        return $fixtures;
    }
}