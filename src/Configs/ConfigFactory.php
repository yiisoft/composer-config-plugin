<?php

namespace Yiisoft\Composer\Config\Configs;

use Yiisoft\Composer\Config\Builder;

/**
 * Config factory creates Config object of proper class
 * according to config name (and maybe other options later).
 */
class ConfigFactory
{
    private static $knownTypes = [
        '__rebuild'     => Rebuild::class,
        '__files'       => System::class,
        'aliases'       => System::class,
        'packages'      => System::class,
        'dotenv'        => DotEnv::class,
        'params'        => Params::class,
        'defines'       => Defines::class,
    ];

    /**
     * @param Builder $builder
     * @param string $name
     * @return Config
     */
    public static function create(Builder $builder, string $name): Config
    {
        $class = static::$knownTypes[$name] ?? Config::class;

        return new $class($builder, $name);
    }
}
