<?php namespace Mayconbordin\L5Fixtures\Loaders;

use Mayconbordin\L5Fixtures\Exceptions\UnsupportedFormatException;
use Mayconbordin\L5Fixtures\FixturesMetadata;

class LoaderFactory
{
    const JSON = "json";
    const CSV  = "csv";
    const YAML = "yaml";

    private static $loaders = [];

    /**
     * Create a loader based on the format.
     *
     * @param string $format
     * @param FixturesMetadata $metadata
     * @return Loader
     * @throws UnsupportedFormatException
     */
    public static function create($format, FixturesMetadata $metadata)
    {
        switch ($format)
        {
            case self::JSON:
                return self::createJsonLoader($metadata);
            case self::CSV:
                return self::createCsvLoader($metadata);
            case self::YAML:
                return self::createYamlLoader($metadata);
            default:
                throw new UnsupportedFormatException($format);
        }
    }

    public static function createJsonLoader(FixturesMetadata $metadata)
    {
        if (!in_array(self::JSON, self::$loaders)) {
            $loader = new JsonLoader();
            $loader->initialize($metadata);

            self::$loaders[self::JSON] = $loader;
        }

        return self::$loaders[self::JSON];
    }

    public static function createCsvLoader(FixturesMetadata $metadata)
    {
        if (!in_array(self::CSV, self::$loaders)) {
            $loader = new CsvLoader();
            $loader->initialize($metadata);

            self::$loaders[self::CSV] = $loader;
        }

        return self::$loaders[self::CSV];
    }

    public static function createYamlLoader(FixturesMetadata $metadata)
    {
        if (!in_array(self::YAML, self::$loaders)) {
            $loader = new YamlLoader();
            $loader->initialize($metadata);

            self::$loaders[self::YAML] = $loader;
        }

        return self::$loaders[self::YAML];
    }
}