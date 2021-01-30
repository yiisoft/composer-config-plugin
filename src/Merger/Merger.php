<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Merger;

use Yiisoft\Composer\Config\Merger\Modifier\ModifierInterface;
use Yiisoft\Composer\Config\Merger\Modifier\ReverseBlockMerge;

final class Merger
{
    /**
     * Merges two or more arrays into one recursively.
     * If each array has an element with the same string key value, the latter
     * will overwrite the former (different from `array_merge_recursive`).
     * Recursive merging will be conducted if both arrays have an element of array
     * type and are having the same key.
     * For integer-keyed elements, the elements from the latter array will
     * be appended to the former array.
     * You can use modifiers {@see Merger::applyModifiers()} to change merging result.
     *
     * @param array ...$args arrays to be merged
     *
     * @return array the merged array (the original arrays are not changed)
     */
    public static function merge(...$args): array
    {
        $lastArray = end($args);
        if (
            isset($lastArray[ReverseBlockMerge::class]) &&
            $lastArray[ReverseBlockMerge::class] instanceof ReverseBlockMerge
        ) {
            return self::applyModifiers(self::performReverseBlockMerge(...$args));
        }

        return self::applyModifiers(self::performMerge(...$args));
    }

    private static function performMerge(array ...$args): array
    {
        $res = array_shift($args) ?: [];
        while (!empty($args)) {
            /** @psalm-var mixed $v */
            foreach (array_shift($args) as $k => $v) {
                if (is_int($k)) {
                    if (array_key_exists($k, $res) && $res[$k] !== $v) {
                        /** @var mixed */
                        $res[] = $v;
                    } else {
                        /** @var mixed */
                        $res[$k] = $v;
                    }
                } elseif (is_array($v) && isset($res[$k]) && is_array($res[$k])) {
                    $res[$k] = self::performMerge($res[$k], $v);
                } else {
                    /** @var mixed */
                    $res[$k] = $v;
                }
            }
        }

        return $res;
    }

    private static function performReverseBlockMerge(array ...$args): array
    {
        $res = array_pop($args) ?: [];
        while (!empty($args)) {
            /** @psalm-var mixed $v */
            foreach (array_pop($args) as $k => $v) {
                if (is_int($k)) {
                    if (array_key_exists($k, $res) && $res[$k] !== $v) {
                        /** @var mixed */
                        $res[] = $v;
                    } else {
                        /** @var mixed */
                        $res[$k] = $v;
                    }
                } elseif (is_array($v) && isset($res[$k]) && is_array($res[$k])) {
                    $res[$k] = self::performReverseBlockMerge($v, $res[$k]);
                } elseif (!isset($res[$k])) {
                    /** @var mixed */
                    $res[$k] = $v;
                }
            }
        }

        return $res;
    }

    /**
     * Apply modifiers (classes that implement {@link ModifierInterface}) in array.
     *
     * For example, {@link \Yiisoft\Composer\Config\Merger\Modifier\UnsetValue} to unset value from previous
     * array or {@link \Yiisoft\Composer\Config\Merger\Modifier\ReplaceArrayValue} to force replace former
     * value instead of recursive merging.
     *
     * @param array $data
     *
     * @return array
     *
     * @see ModifierInterface
     */
    private static function applyModifiers(array $data): array
    {
        $modifiers = [];
        /** @psalm-var mixed $v */
        foreach ($data as $k => $v) {
            if ($v instanceof ModifierInterface) {
                $modifiers[$k] = $v;
                unset($data[$k]);
            } elseif (is_array($v)) {
                $data[$k] = self::applyModifiers($v);
            }
        }
        ksort($modifiers);
        foreach ($modifiers as $key => $modifier) {
            $data = $modifier->apply($data, $key);
        }
        return $data;
    }
}
