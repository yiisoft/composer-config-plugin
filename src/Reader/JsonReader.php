<?php

namespace Yiisoft\Composer\Config\Reader;

/**
 * JsonReader - reads PHP files.
 */
class JsonReader extends AbstractReader
{
    protected function readRaw(string $path)
    {
        return json_decode($this->getFileContents($path), true, 512, JSON_THROW_ON_ERROR);
    }
}
