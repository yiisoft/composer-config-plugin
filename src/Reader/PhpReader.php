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
        $result = static function () {
            /** @noinspection NonSecureExtractUsageInspection */
            extract(func_get_arg(0));

            return require func_get_arg(1);
        };

        return $result($this->builder->getVars(), $path);
    }
}
