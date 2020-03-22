<?php
namespace Yiisoft\Composer\Config\Readers;

/**
 * JsonReader - reads PHP files.
 */
class JsonReader extends AbstractReader
{
    public function readRaw($path)
    {
        return json_decode($this->getFileContents($path), true);
    }
}
