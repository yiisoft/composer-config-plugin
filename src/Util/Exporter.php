<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Util;

use Yiisoft\VarDumper\VarDumper;

/**
 * Exporter is a thin wrapper for actual exporting implementation.
 * This class should be kept to allow change implementation easily.
 */
class Exporter
{
    /**
     * Returns PHP-executable string representation of given value.
     * @param mixed $value
     * @return string
     */
    public static function exportVar($value): string
    {
        return VarDumper::create($value)->asPhpString();
    }
}
