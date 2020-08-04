<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Tests\Integration\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Composer\Config\Builder;

abstract class PluginTestCase extends TestCase
{
    private const BASE_DIRECTORY = __DIR__ . '/../Environments';

    private static array $configs = [];

    protected function registerConfig(string $environment, string $name): void
    {
        if ((self::$configs[$environment][$name] ?? []) !== []) {
            return;
        }
        self::$configs[$environment][$name] = require Builder::path($name, self::BASE_DIRECTORY . '/' . $environment);
    }

    protected function getFromConfig(string $environment, string $config, string $name)
    {
        return self::$configs[$environment][$config][$name];
    }
}
