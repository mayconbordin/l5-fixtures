<?php namespace Mayconbordin\L5Fixtures\Loaders;

class JsonLoader extends AbstractLoader
{
    public function load($path)
    {
        $data = $this->metadata->getFilesystem()->read($path);
        return json_decode($data, true);
    }
}