<?php

namespace Yiisoft\Composer\Config\Config;

/**
 * DotEnv class represents output configuration file with ENV values.
 */
class Envs extends Config
{
    public function envsRequired(): bool
    {
        return false;
    }

    public function constantsRequired(): bool
    {
        return false;
    }

    public function paramsRequired(): bool
    {
        return false;
    }
}
