<?php namespace Mayconbordin\L5Fixtures\Loaders;

use Mayconbordin\L5Fixtures\Exceptions\UnsupportedFormatException;
use Mayconbordin\L5Fixtures\FixturesMetadata;

class LoaderFactory
{
    const JSON = "json";
    const CSV  = "csv";
    const YAML = "yaml";
    const PHP  = "php";

    private static $loaders = [];

    /**
     * Create a loader based on the format.
     *
     * @param string $format
     * @param FixturesMetadata $metadata
     * @return Loader
     * @throws UnsupportedFormatException
     */
    public static function create($format, FixturesMetadata $metadata, $cache = true)
    {
        if ($cache === true && isset(self::$loaders[$format])) {
            return self::$loaders[$format];
        }

        $loader = null;

        switch ($format)
        {
            case self::JSON:
                $loader = new JsonLoader();
                break;

            case self::CSV:
                $loader = new CsvLoader();
                break;

            case self::YAML:
                $loader = new YamlLoader();
                break;

            case self::PHP:
                $loader = new PhpLoader();
                break;

            default:
                throw new UnsupportedFormatException($format);
                break;
        }

        $loader->initialize($metadata);

        if ($cache === true) {
            self::$loaders[$format] = $loader;
        }

        return $loader;
    }
}