<?php

namespace Yiisoft\Composer\Config\Config;

/**
 * Params class represents output configuration file with params definitions.
 */
class Params extends Config
{
    protected function calcValues(array $sources): array
    {
        return $this->pushEnvVars(parent::calcValues($sources));
    }

    protected function pushEnvVars(array $vars): array
    {
        if (empty($vars)) {
            return [];
        }

        $env = $this->builder->getConfig('envs')->getValues();

        foreach ($vars as $key => &$value) {
            if (is_array($value)) {
                foreach (array_keys($value) as $subkey) {
                    $envKey = $this->getEnvKey($key . '_' . $subkey);
                    if (array_key_exists($envKey, $env)) {
                        $value[$subkey] = $env[$envKey];
                    }
                }
            } else {
                $envKey = $this->getEnvKey($key);
                if (array_key_exists($envKey, $env)) {
                    $vars[$key] = $env[$envKey];
                }
            }
        }

        return $vars;
    }

    private function getEnvKey(string $key): string
    {
        return strtoupper(strtr($key, '.-', '__'));
    }

    protected function paramsRequired(): bool
    {
        return false;
    }
}
