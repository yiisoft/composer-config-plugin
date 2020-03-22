<?php

namespace Yiisoft\Composer\Config\Configs;

/**
 * Defines class represents output configuration file with constant definitions.
 */
class Defines extends Config
{
    protected function loadFile($path): array
    {
        parent::loadFile($path);
        if (pathinfo($path, PATHINFO_EXTENSION) !== 'php') {
            return [];
        }

        return [$path];
    }

    public function buildRequires(): string
    {
        $res = [];
        foreach ($this->values as $path) {
            $res[] = "require_once '$path';";
        }

        return implode("\n", $res);
    }
}
