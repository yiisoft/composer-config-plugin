<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Config;

use Yiisoft\Composer\Config\Builder;

/**
 * Config factory creates Config object of proper class
 * according to config name (and maybe other options later).
 */
class ConfigOutputFactory
{
    private const KNOWN_TYPES = [
        '__files' => System::class,
        'packages' => System::class,
        'envs' => Envs::class,
        'params' => Params::class,
        'constants' => Constants::class,
    ];

    public function create(Builder $builder, string $name): ConfigOutput
    {
        $class = self::KNOWN_TYPES[$name] ?? ConfigOutput::class;

        return new $class($builder, $name);
    }
}
