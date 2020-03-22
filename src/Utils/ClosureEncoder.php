<?php
namespace Yiisoft\Composer\Config\Utils;

use Closure;
use Riimu\Kit\PHPEncoder\Encoder\Encoder;
use Opis\Closure\ReflectionClosure;

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
        return (new ReflectionClosure($value))->getCode();
    }
}
