<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Util;

use Closure;
use Riimu\Kit\PHPEncoder\Encoder\Encoder;

class ObjectEncoder implements Encoder
{
    public function getDefaultOptions(): array
    {
        return [];
    }

    public function supports($value): bool
    {
        return is_object($value) && !$value instanceof Closure;
    }

    public function encode($value, $depth, array $options, callable $encode)
    {
        $serializedValue = \Opis\Closure\serialize($value);

        return '\Opis\Closure\unserialize(' . Helper::exportVar($serializedValue) . ')';
    }
}
