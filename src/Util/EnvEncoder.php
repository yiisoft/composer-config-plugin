<?php

declare(strict_types=1);

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

        $closureReflection = $reflection->getClosureScopeClass();
        $closureClassOwnerName = $closureReflection->getName();

        if (!is_a($closureClassOwnerName, Env::class, true)) {
            return false;
        }

        return strpos($reflection->getCode(), 'static fn() => $_ENV') !== false;
    }

    public function encode($value, $depth, array $options, callable $encode)
    {
        $reflection = new ReflectionClosure($value);
        $variables = $reflection->getStaticVariables();
        $key = $variables['key'];
        $default = $variables['default'] ?? null;

        return str_replace(
            ['$key', '$default'],
            ["'$key'", Helper::exportVar($default)],
            substr(
                $reflection->getCode(),
                15
            ),
        );
    }
}
