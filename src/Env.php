<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config;

final class Env
{
    public static function get(string $key, $default = null): callable
    {
        if (count(func_get_args()) === 2) {
            return static fn () => getenv($key) ?? $default;
        }

        return static fn () => getenv($key);
    }
}
