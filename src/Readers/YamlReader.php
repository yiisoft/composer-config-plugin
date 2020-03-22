<?php

namespace Yiisoft\Composer\Config\Readers;

use Yiisoft\Composer\Config\exceptions\UnsupportedFileTypeException;
use Symfony\Component\Yaml\Yaml;

/**
 * YamlReader - reads YAML files.
 */
class YamlReader extends AbstractReader
{
    public function readRaw($path)
    {
        if (!class_exists(Yaml::class)) {
            throw new UnsupportedFileTypeException("for YAML support require `symfony/yaml` in your composer.json (reading $path)");
        }

        return Yaml::parse($this->getFileContents($path));
    }
}
