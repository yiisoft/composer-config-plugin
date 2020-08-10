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
            foreach (func_get_arg(0) as $__k => $__v) {
                if ($__k === 'params') {
                    $params = $__v;
                } else {
                    $config[$__k] = $__v;
                }
            }
            unset($__k, $__v);

            return require func_get_arg(1);
        };

        return $result($this->builder->getVars(), $path);
    }
}
