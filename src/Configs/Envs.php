<?php

namespace Yiisoft\Composer\Config\Configs;

/**
 * DotEnv class represents output configuration file with ENV values.
 */
class Envs extends Config
{
    public function envRequires(): bool
    {
        return false;
    }

    public function constantsRequires(): bool
    {
        return false;
    }

    public function paramsRequires(): bool
    {
        return false;
    }
}
