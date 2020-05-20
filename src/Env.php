<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config;

class Env
{
    public static function get(string $key): callable
    {
        return static fn() => $_ENV[$key];
    }
}
