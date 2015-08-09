<?php namespace Mayconbordin\L5Fixtures\Loaders;


class PhpLoader extends AbstractLoader
{

    public function load($path)
    {
        $path = $this->metadata->getPath() . '/' . $path;
        $data = include $path;
        return $data;
    }
}