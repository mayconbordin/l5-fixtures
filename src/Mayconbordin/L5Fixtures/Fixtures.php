<?php namespace Mayconbordin\L5Fixtures;

use Illuminate\Database\Eloquent\Model;
use League\Flysystem\Filesystem;
use League\Flysystem\Plugin\ListFiles;
use League\Flysystem\Adapter\Local;
use League\Csv\Reader;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

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

        if (!is_dir($location)) {
            throw new \Exception("Fixtures location '$location' is not a directory.");
        }

        if (!file_exists($location)) {
            throw new \Exception("Fixtures location '$location' does not exists.");
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
        $fixtures = is_null($allowed) ? $this->getFixtures() : array_intersect_key($this->getFixtures(), array_flip($allowed));

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
        $fixtures = is_null($allowed) ? $this->getFixtures() : array_intersect_key($this->getFixtures(), array_flip($allowed));

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

    public function getFixtures()
    {
        if ($this->fixtures == null) {
            $this->setUp();
        }

        return $this->fixtures;
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
                throw new \Exception("Format '{$fixture->format}' is not supported.");
        }
    }

    protected function loadFixturesMetadata($dir) {
        $adapter    = new Local($dir);
        $this->filesystem = new Filesystem($adapter);
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