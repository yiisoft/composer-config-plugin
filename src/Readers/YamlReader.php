<?php

namespace Yiisoft\Composer\Config\Readers;

use Symfony\Component\Yaml\Yaml;
use Yiisoft\Composer\Config\exceptions\UnsupportedFileTypeException;

/**
 * YamlReader - reads YAML files.
 */
class YamlReader extends AbstractReader
{
    protected function readRaw($path)
    {
        if (!class_exists(Yaml::class)) {
            throw new UnsupportedFileTypeException("for YAML support require `symfony/yaml` in your composer.json (reading $path)");
        }

        return Yaml::parse($this->getFileContents($path));
    }
}
