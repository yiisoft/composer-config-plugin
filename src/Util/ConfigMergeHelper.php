<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Util;

use Yiisoft\Arrays\ReplaceArrayValue;
use Yiisoft\Arrays\UnsetArrayValue;

use function in_array;
use function is_array;
use function is_int;

final class ConfigMergeHelper
{
    /**
     * Merges two or more arrays into one recursively.
     *
     * @param array[] $arrays
     * @return array the merged array
     */
    public static function mergeConfig(array ...$arrays): array
    {
        $result = array_shift($arrays) ?: [];
        foreach ($arrays as $items) {
            if (!is_array($items)) {
                continue;
            }
            foreach ($items as $key => $value) {
                if ($value instanceof UnsetArrayValue) {
                    unset($result[$key]);
                    continue;
                }

                if ($value instanceof ReplaceArrayValue) {
                    $result[$key] = $value->value;
                    continue;
                }

                if (is_int($key)) {
                    /// XXX skip repeated values
                    if (in_array($value, $result, true)) {
                        continue;
                    }

                    if (array_key_exists($key, $result)) {
                        $result[] = $value;
                    }

                    continue;
                }

                if (is_array($value) && array_key_exists($key, $result) && is_array($result[$key])) {
                    $result[$key] = self::mergeConfig($result[$key], $value);
                    continue;
                }

                $result[$key] = $value;
            }
        }

        return $result;
    }
}
