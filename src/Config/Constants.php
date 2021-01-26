<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Config;

use Yiisoft\Arrays\Collection\ArrayCollection;

/**
 * Defines class represents output configuration file with constant definitions.
 */
class Constants extends ConfigOutput
{
    protected function loadFile(string $path): ArrayCollection
    {
        parent::loadFile($path);
        if (pathinfo($path, PATHINFO_EXTENSION) !== 'php') {
            return new ArrayCollection();
        }

        return new ArrayCollection([$path]);
    }

    public function buildRequires(): string
    {
        $res = [];
        foreach ($this->values as $path) {
            $res[] = "require_once '$path';";
        }

        return implode("\n", $res);
    }

    protected function constantsRequired(): bool
    {
        return false;
    }

    protected function paramsRequired(): bool
    {
        return false;
    }
}
