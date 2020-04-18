<?php

namespace Yiisoft\Composer\Config\Readers;

use Symfony\Component\Yaml\Yaml;
use Yiisoft\Composer\Config\Exceptions\UnsupportedFileTypeException;

/**
 * YamlReader - reads YAML files.
 */
class YamlReader extends AbstractReader
{
    protected function readRaw(string $path)
    {
        if (!class_exists(Yaml::class)) {
            throw new UnsupportedFileTypeException("For YAML support require `symfony/yaml` in your composer.json (reading $path)");
        }

        return Yaml::parse($this->getFileContents($path));
    }
}
