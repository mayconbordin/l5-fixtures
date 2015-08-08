<?php namespace Mayconbordin\L5Fixtures;

use League\Flysystem\Filesystem;
use League\Flysystem\Plugin\ListFiles;
use League\Flysystem\Adapter\Local;

class FixturesMetadata
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var array
     */
    protected $fixtures;

    /**
     * FixturesMetadata constructor.
     *
     * @param string $path The path where the fixtures are stored.
     * @param bool $autoload If the fixtures metadata is to be automatically loaded.
     */
    public function __construct($path, $autoload = true)
    {
        $this->path = $path;

        if ($autoload) {
            $this->load();
        }
    }

    /**
     * Load the fixtures from the informed path.
     */
    public function load()
    {
        $this->fixtures = [];
        $files = $this->getFilesystem()->listFiles();

        foreach ($files as $file)
        {
            $fixture = new \stdClass();
            $fixture->table  = $file['filename'];
            $fixture->format = $file['extension'];
            $fixture->path   = $file['path'];

            $this->fixtures[$fixture->table] = $fixture;
        }
    }

    /**
     * @return array
     */
    public function getFixtures()
    {
        return $this->fixtures;
    }

    /**
     * @return Filesystem
     */
    public function getFilesystem()
    {
        if ($this->filesystem == null) {
            $this->filesystem = new Filesystem(new Local($this->path));
            $this->filesystem->addPlugin(new ListFiles());
        }

        return $this->filesystem;
    }

}