<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Tests\Integration\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Composer\Config\Builder;

abstract class PluginTestCase extends TestCase
{
    private const BASE_DIRECTORY = __DIR__ . '/../Environment';

    private static array $configs = [];

    protected function registerConfig(string $name): void
    {
        if ((self::$configs[$name] ?? []) !== []) {
            return;
        }
        self::$configs[$name] = require Builder::path($name, self::BASE_DIRECTORY);
    }

    protected function getFromConfig(string $config, string $name)
    {
        return self::$configs[$config][$name];
    }
}
