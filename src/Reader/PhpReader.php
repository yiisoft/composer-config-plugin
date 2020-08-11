<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Reader;

/**
 * PhpReader - reads PHP files.
 */
class PhpReader extends AbstractReader
{
    protected function readRaw(string $path)
    {
        $params = [];
        $config = [];

        foreach ($this->builder->getVars() as $key => $parameters) {
            if ($key === 'params') {
                $params = (array)$parameters;
            } else {
                $config[$key] = $parameters;
            }
        }

        $result = static function (array $params, array $config) {
            return require func_get_arg(2);
        };

        return $result($params, $config, $path);
    }
}
