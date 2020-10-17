<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Util;

use Closure;
use Riimu\Kit\PHPEncoder\Encoder\Encoder;
use Yiisoft\VarDumper\VarDumper;

/**
 * Closure encoder for Riimu Kit-PHPEncoder.
 */
class ClosureEncoder implements Encoder
{
    public function getDefaultOptions(): array
    {
        return [];
    }

    public function supports($value): bool
    {
        return $value instanceof Closure;
    }

    public function encode($value, $depth, array $options, callable $encode)
    {
        return VarDumper::create($value)->asPhpString();
    }
}
