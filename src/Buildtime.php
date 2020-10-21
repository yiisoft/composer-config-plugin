<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config;

/**
 * Buildtime marker class. Used as marker only.
 * Everything is not evaluated at compile time by default except Buildtime::* calls.
 * @see https://gist.github.com/samdark/86f2b9ff01a96892efbbf254eca8482d
 */
final class Buildtime
{
    /**
     * @param mixed $code will not be evaluated when processed with the plugin.
     */
    public static function run($code)
    {
        return $code;
    }
}
