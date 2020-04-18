<?php

namespace Yiisoft\Composer\Config\Configs;

/**
 * DotEnv class represents output configuration file with ENV values.
 */
class Envs extends Config
{
    protected function envsRequired(): bool
    {
        return false;
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
