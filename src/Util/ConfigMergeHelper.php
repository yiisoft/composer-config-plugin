<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Util;

use Yiisoft\Arrays\ReplaceArrayValue;
use Yiisoft\Arrays\UnsetArrayValue;

use function func_get_args;
use function in_array;
use function is_array;
use function is_int;

final class ConfigMergeHelper
{
    /**
     * Merges two or more arrays into one recursively.
     *
     * @return array the merged array
     */
    public static function mergeConfig(): array
    {
        $args = func_get_args();
        $res = array_shift($args) ?: [];
        foreach ($args as $items) {
            if (!is_array($items)) {
                continue;
            }
            foreach ($items as $k => $v) {
                if ($v instanceof UnsetArrayValue) {
                    unset($res[$k]);
                } elseif ($v instanceof ReplaceArrayValue) {
                    $res[$k] = $v->value;
                } elseif (is_int($k)) {
                    /// XXX skip repeated values
                    if (in_array($v, $res, true)) {
                        continue;
                    }
                    if (isset($res[$k])) {
                        $res[] = $v;
                    } else {
                        $res[$k] = $v;
                    }
                } elseif (is_array($v) && isset($res[$k]) && is_array($res[$k])) {
                    $res[$k] = self::mergeConfig($res[$k], $v);
                } else {
                    $res[$k] = $v;
                }
            }
        }

        return $res;
    }
}
