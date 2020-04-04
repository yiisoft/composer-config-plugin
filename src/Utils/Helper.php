<?php

namespace Yiisoft\Composer\Config\Utils;

use ReflectionException;
use Riimu\Kit\PHPEncoder\PHPEncoder;

use function in_array;
use function is_array;
use function is_int;

/**
 * Helper class.
 */
class Helper
{
    private PHPEncoder $encoder;

    public function __construct()
    {
        $encoder = new PHPEncoder([
            'object.format' => 'serialize',
        ]);
        $encoder->addEncoder(new ClosureEncoder(), true);
        $this->encoder = $encoder;
    }

    /**
     * @param array $args
     * @return array the merged array
     */
    public function mergeConfig(...$args): array
    {
        $res = array_shift($args) ?: [];
        foreach ($args as $items) {
            if (!is_array($items)) {
                continue;
            }
            foreach ($items as $k => $v) {
                if (is_int($k)) {
                    /// XXX skip repeated values
                    if (in_array($v, $res, true)) {
                        continue;
                    }
                    if (array_key_exists($k, $res)) {
                        $res[] = $v;
                    } else {
                        $res[$k] = $v;
                    }
                } elseif (is_array($v) && isset($res[$k]) && is_array($res[$k])) {
                    $res[$k] = $this->mergeConfig($res[$k], $v);
                } else {
                    $res[$k] = $v;
                }
            }
        }

        return $res;
    }

    public function fixConfig(array $config): array
    {
        $remove = false;
        foreach ($config as $key => &$value) {
            if (is_array($value)) {
                $value = $this->fixConfig($value);
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

    /**
     * Returns PHP-executable string representation of given value.
     * Uses Riimu/Kit-PHPEncoder based `var_export` alternative.
     * And Opis/Closure to dump closures as PHP code.
     *
     * @param mixed $value
     * @return string
     * @throws ReflectionException
     */
    public function exportVar($value): string
    {
        return $this->encoder->encode($value);
    }
}
