<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Util;

use Closure;
use Riimu\Kit\PHPEncoder\Encoder\Encoder;
use Yiisoft\Composer\Config\Builder;
use Yiisoft\VarDumper\VarDumper;

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
        $reflection = new \ReflectionFunction($value);

        $closureScopeClass = $reflection->getClosureScopeClass();
        if ($closureScopeClass === null) {
            return false;
        }
        $closureClassOwnerName = $closureScopeClass->getName();

        return is_a($closureClassOwnerName, Builder::class, true);
    }

    public function encode($value, $depth, array $options, callable $encode)
    {
        $reflection = new \ReflectionFunction($value);
        $variables = $reflection->getStaticVariables();
        $config = $variables['config'];

        return str_replace(
            ['$config'],
            ["'$config.php'"],
            substr(
                VarDumper::create($value)->asPhpString(),
                16
            ),
        );
    }
}
