<?php

namespace Yiisoft\Composer\Config\Util;

use Closure;
use Opis\Closure\ReflectionClosure;
use Riimu\Kit\PHPEncoder\Encoder\Encoder;
use Yiisoft\Composer\Config\Env;

class EnvEncoder implements Encoder
{
    public function getDefaultOptions(): array
    {
        return [];
    }

    public function supports($value): bool
    {
        if (!$value instanceof Closure) {
            return false;
        }
        $reflection = new ReflectionClosure($value);

        $closureReflection = ($reflection)->getClosureScopeClass();
        $closureClassOwnerName = $closureReflection->getName();

        if ($closureClassOwnerName !== Env::class && !is_subclass_of($closureClassOwnerName, Env::class)) {
            return false;
        }

        return strpos($reflection->getCode(), 'fn() => $_ENV') !== false;
    }

    public function encode($value, $depth, array $options, callable $encode)
    {
        $reflection = new ReflectionClosure($value);
        $value = current($reflection->getStaticVariables());

        return substr(
            str_replace('$key', "'$value'", $reflection->getCode()),
            8,
            -1
        );
    }
}
