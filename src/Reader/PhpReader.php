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
        $params = $this->builder->getVars()['params'] ?? [];

        $result = static function (array $params) {
            return require func_get_arg(1);
        };

        /** @psalm-suppress TooManyArguments */
        return $result($params, $path);
    }
}
