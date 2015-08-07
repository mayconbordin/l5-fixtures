<?php namespace Mayconbordin\L5Fixtures;

use Illuminate\Database\Eloquent\Model;
use League\Flysystem\Filesystem;
use League\Flysystem\Plugin\ListFiles;
use League\Flysystem\Adapter\Local;
use League\Csv\Reader;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Mayconbordin\L5Fixtures\Exceptions\DirectoryNotFoundException;
use Mayconbordin\L5Fixtures\Exceptions\NotDirectoryException;
use Mayconbordin\L5Fixtures\Exceptions\UnsupportedFormatException;

class Fixtures
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var array
     */
    protected $fixtures = null;

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

        $this->loadFixturesMetadata($location);
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
        $rows = $this->readFixture($fixture);

        DB::table($fixture->table)->insert($rows);
    }

    public function getFixtures($allowed = null)
    {
        if ($this->fixtures == null) {
            $this->setUp();
        }

        if (is_null($allowed)) {
            $fixtures = $this->fixtures;
        } else {
            $fixtures = array_intersect_key($this->fixtures, array_flip($allowed));
        }

        return $fixtures;
    }

    protected function readFixture($fixture)
    {
        $data = $this->filesystem->read($fixture->path);

        switch ($fixture->format)
        {
            case 'json':
                return json_decode($data, true);

            case 'csv':
                $csv = Reader::createFromString($data);
                $delimiters = $csv->detectDelimiterList(10, ['|']);
                $csv->setDelimiter(array_values($delimiters)[0]);

                return $csv->fetchAssoc();

            default:
                throw new UnsupportedFormatException($fixture->format);
        }
    }

    protected function loadFixturesMetadata($dir) {
        $this->filesystem = new Filesystem(new Local($dir));
        $this->filesystem->addPlugin(new ListFiles());

        $files = $this->filesystem->listFiles();
        $this->fixtures = [];

        foreach ($files as $file)
        {
            $fixture = new \stdClass();
            $fixture->table  = $file['filename'];
            $fixture->format = $file['extension'];
            $fixture->path   = $file['path'];

            $this->fixtures[$fixture->table] = $fixture;
        }
    }
}