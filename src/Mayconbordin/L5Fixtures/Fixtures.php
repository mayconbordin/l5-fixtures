<?php namespace Mayconbordin\L5Fixtures;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use Mayconbordin\L5Fixtures\Exceptions\DirectoryNotFoundException;
use Mayconbordin\L5Fixtures\Exceptions\InvalidDataSchemaException;
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
        $this->setForeignKeyChecks(false);

        $command = $this->truncateCommand();

        foreach ($fixtures as $fixture)
        {
            DB::table($fixture->table)->$command();
        }

        $this->setForeignKeyChecks(true);
    }

    protected function loadFixtures($allowed = null)
    {
        $fixtures = $this->getFixtures($allowed);

        Model::unguard();
        $this->setForeignKeyChecks(false);

        foreach ($fixtures as $fixture)
        {
            $this->loadFixture($fixture);
        }

        $this->setForeignKeyChecks(true);
    }

    protected function loadFixture($fixture)
    {
        $rows = LoaderFactory::create($fixture->format, $this->metadata)->load($fixture->path);

        if (!is_array($rows)) {
            throw new InvalidDataSchemaException($fixture);
        }

        $columnCount  = count($rows[0]);
        $chunkSize    = array_get($this->config, 'chunk_size', 500);
        $rowsPerChunk = (integer) ($chunkSize / $columnCount);

        // Convert the string "null" into null
        array_walk_recursive($rows, function(&$item, $key) {
            if (!is_array($item) && strcasecmp($item, "null") == 0) {
                $item = null;
            }
        });

        // Insert the chunks in the database
        foreach (array_chunk($rows, $rowsPerChunk) as $chunk) {
            DB::table($fixture->table)->insert($chunk);
        }
    }

    public function getFixtures($allowed = null)
    {
        if ($this->metadata == null) {
            $this->setUp();
        }

        if (is_null($allowed)) {
            $fixtures = $this->metadata->getFixtures();
        } else {
            if (!is_array($allowed)) {
                $allowed = [$allowed];
            }
            $fixtures = array_intersect_key($this->metadata->getFixtures(), array_flip($allowed));
        }

        return $fixtures;
    }

    protected function setForeignKeyChecks ($enable = false)
    {
        switch(DB::getDriverName()) {
            case 'mysql':
                $status = $enable ? 1 : 0;
                DB::statement("SET FOREIGN_KEY_CHECKS=$status;");
                break;

            case 'sqlite':
                $status = $enable ? 'ON' : 'OFF';
                DB::statement("PRAGMA foreign_keys = $status");
                break;

            case 'pgsql':
                $status = $enable ? 'DEFAULT' : 'replica';
                DB::statement("SET session_replication_role = $status");
                break;
        }
    }

    protected function truncateCommand ()
    {
        switch(DB::getDriverName()) {
            case 'pgsql':
                return 'delete';

            default:
                return 'truncate';
        }
    }
}
