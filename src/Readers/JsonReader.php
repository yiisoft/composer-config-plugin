<?php

namespace Yiisoft\Composer\Config\Readers;

/**
 * JsonReader - reads PHP files.
 */
class JsonReader extends AbstractReader
{
    protected function readRaw($path)
    {
        return json_decode($this->getFileContents($path), true, 512, JSON_THROW_ON_ERROR);
    }
}
