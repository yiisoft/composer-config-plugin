<?php

namespace Yiisoft\Composer\Config\Configs;

/**
 * DotEnv class represents output configuration file with ENV values.
 */
class DotEnv extends Config
{
    public function hasEnv(): bool
    {
        return false;
    }

    public function hasConstants(): bool
    {
        return false;
    }

    public function hasParams(): bool
    {
        return false;
    }
}
