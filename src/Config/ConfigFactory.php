<?php

namespace Yiisoft\Composer\Config\Config;

use Yiisoft\Composer\Config\Builder;

/**
 * Config factory creates Config object of proper class
 * according to config name (and maybe other options later).
 */
class ConfigFactory
{
    private const KNOWN_TYPES = [
        '__rebuild' => Rebuild::class,
        '__files' => System::class,
        'aliases' => System::class,
        'packages' => System::class,
        'envs' => Envs::class,
        'params' => Params::class,
        'constants' => Constants::class,
    ];

    public function create(Builder $builder, string $name): Config
    {
        $class = self::KNOWN_TYPES[$name] ?? Config::class;

        return new $class($builder, $name);
    }
}
