<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Util;

use Closure;
use Riimu\Kit\PHPEncoder\Encoder\Encoder;
use Yiisoft\Composer\Config\Env;
use Yiisoft\VarDumper\VarDumper;

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
        $reflection = new \ReflectionFunction($value);

        $closureScopeClass = $reflection->getClosureScopeClass();
        if ($closureScopeClass === null) {
            return false;
        }
        $closureClassOwnerName = $closureScopeClass->getName();

        return is_a($closureClassOwnerName, Env::class, true);
    }

    public function encode($value, $depth, array $options, callable $encode)
    {
        $reflection = new \ReflectionFunction($value);
        $variables = $reflection->getStaticVariables();
        $key = $variables['key'];
        $default = $variables['default'] ?? null;

        return str_replace(
            ['$key', '$default'],
            ["'$key'", Helper::exportVar($default)],
            substr(
                VarDumper::create($value)->asPhpString(),
                16
            ),
        );
    }
}
