<?php

namespace Yiisoft\Composer\Config\Utils;

use Riimu\Kit\PHPEncoder\PHPEncoder;

/**
 * Helper class.
 */
class Helper
{
    /**
     * Merges two or more arrays into one recursively.
     * Based on Yii2 yii\helpers\BaseArrayHelper::merge.
     * @return array the merged array
     */
    public static function mergeConfig(): array
    {
        $args = \func_get_args();
        $res = array_shift($args) ?: [];
        foreach ($args as $items) {
            if (!\is_array($items)) {
                continue;
            }
            foreach ($items as $k => $v) {
                if ($v instanceof \yii\helpers\UnsetArrayValue || $v instanceof \Yiisoft\Arrays\UnsetArrayValue) {
                    unset($res[$k]);
                } elseif ($v instanceof \yii\helpers\ReplaceArrayValue || $v instanceof \Yiisoft\Arrays\ReplaceArrayValue) {
                    $res[$k] = $v->value;
                } elseif (\is_int($k)) {
                    /// XXX skip repeated values
                    if (\in_array($v, $res, true)) {
                        continue;
                    }
                    if (isset($res[$k])) {
                        $res[] = $v;
                    } else {
                        $res[$k] = $v;
                    }
                } elseif (\is_array($v) && isset($res[$k]) && \is_array($res[$k])) {
                    $res[$k] = self::mergeConfig($res[$k], $v);
                } else {
                    $res[$k] = $v;
                }
            }
        }

        return $res;
    }

    public static function fixConfig(array $config): array
    {
        $remove = false;
        foreach ($config as $key => &$value) {
            if (is_array($value)) {
                $value = static::fixConfig($value);
            } elseif ($value instanceof RemoveArrayKeys) {
                $remove = true;
                unset($config[$key]);
            }
        }
        if ($remove) {
            return array_values($config);
        }

        return $config;
    }

    public static function exportDefines(array $defines): string
    {
        $res = '';
        foreach ($defines as $key => $value) {
            $var = static::exportVar($value);
            $res .= "defined('$key') or define('$key', $var);\n";
        }

        return $res;
    }

    /**
     * Returns PHP-executable string representation of given value.
     * Uses Riimu/Kit-PHPEncoder based `var_export` alternative.
     * And Opis/Closure to dump closures as PHP code.
     * @param mixed $value
     * @return string
     * @throws \ReflectionException
     */
    public static function exportVar($value): string
    {
        return static::getEncoder()->encode($value);
    }

    private static $encoder;

    private static function getEncoder()
    {
        if (self::$encoder === null) {
            self::$encoder = static::createEncoder();
        }

        return self::$encoder;
    }

    private static function createEncoder()
    {
        $encoder = new PHPEncoder([
            'object.format' => 'serialize',
        ]);
        $encoder->addEncoder(new ClosureEncoder(), true);

        return $encoder;
    }
}
