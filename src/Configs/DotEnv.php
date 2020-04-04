<?php

namespace Yiisoft\Composer\Config\Configs;

/**
 * DotEnv class represents output configuration file with ENV values.
 */
class DotEnv extends Config
{
    public function hasEnv(): bool
    {
        return true;
    }

    public function hasConstants(): bool
    {
        return true;
    }

    public function hasParams(): bool
    {
        return true;
    }
}
