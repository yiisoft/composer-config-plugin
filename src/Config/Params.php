<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Config;

/**
 * Params class represents output configuration file with params definitions.
 */
class Params extends ConfigOutput
{
    protected function calcValues(array $sources): array
    {
        return $this->pushEnvVars(parent::calcValues($sources));
    }

    protected function pushEnvVars(array $data): array
    {
        if (empty($data)) {
            return [];
        }

        $env = $this->builder->getConfig('envs')->getValues();

        return self::pushValues($data, $env);
    }

    public static function pushValues(array $data, array $values, string $prefix = null)
    {
        foreach ($data as $key => &$value) {
            $subkey = $prefix===null ? $key : "${prefix}_$key";

            $envkey = self::getEnvKey($subkey);
            if (isset($values[$envkey])) {
                $value = $values[$envkey];
            } elseif (is_array($value)) {
                $value = self::pushValues($value, $values, $subkey);
            }
        }

        return $data;
    }

    private static function getEnvKey(string $key): string
    {
        return strtoupper(strtr($key, '.-', '__'));
    }

    protected function paramsRequired(): bool
    {
        return false;
    }
}
