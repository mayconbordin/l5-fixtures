<?php namespace Mayconbordin\L5Fixtures\Loaders;

use Symfony\Component\Yaml\Yaml;

class YamlLoader extends AbstractLoader
{
    public function load($path)
    {
        $data = $this->metadata->getFilesystem()->read($path);
        return Yaml::parse($data);
    }
}