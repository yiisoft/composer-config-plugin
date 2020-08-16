<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Util;

use Closure;
use Opis\Closure\ReflectionClosure;
use Riimu\Kit\PHPEncoder\Encoder\Encoder;
use Yiisoft\Composer\Config\Builder;

class BuilderRequireEncoder implements Encoder
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

        return is_a($closureClassOwnerName, Builder::class, true);
    }

    public function encode($value, $depth, array $options, callable $encode)
    {
        $reflection = new ReflectionClosure($value);
        $variables = $reflection->getStaticVariables();
        $config = $variables['config'];

        return str_replace(
            ['$config'],
            ["'$config.php'"],
            substr(
                $reflection->getCode(),
                16
            ),
        );
    }
}
