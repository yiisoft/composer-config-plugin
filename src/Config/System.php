<?php

namespace Yiisoft\Composer\Config\Config;

/**
 * System class represents system configuration files:
 * __files, aliases, packages.
 */
class System extends Config
{
    public function setValue(string $name, $value): Config
    {
        $this->values[$name] = $value;

        return $this;
    }

    public function setValues(array $values): Config
    {
        $this->values = $values;

        return $this;
    }

    public function mergeValues(array $values): Config
    {
        $this->values = array_merge($this->values, $values);

        return $this;
    }

    public function load(array $paths = []): Config
    {
        $path = $this->getOutputPath();
        if (!file_exists($path)) {
            return $this;
        }

        $this->values = array_merge($this->loadFile($path), $this->values);

        return $this;
    }

    public function build(): Config
    {
        $this->values = $this->substituteOutputDirs($this->values);

        return $this;
    }
}
