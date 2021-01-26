<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Config;

/**
 * System class represents system configuration files:
 * __files, packages.
 */
class System extends ConfigOutput
{
    public function setValue(string $name, $value): self
    {
        $this->values[$name] = $value;

        return $this;
    }

    public function setValues(array $values): self
    {
        $this->values = $values;

        return $this;
    }

    public function mergeValues(array $values): self
    {
        $this->values = array_merge($this->values, $values);

        return $this;
    }

    public function load(array $paths = []): self
    {
        $path = $this->getOutputPath();
        if (!file_exists($path)) {
            return $this;
        }

        $this->values = array_merge($this->loadFile($path), $this->values);

        return $this;
    }

    public function build(): self
    {
        $this->values = $this->substituteOutputDirs($this->values);

        return $this;
    }
}
