<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Tests\Integration\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Composer\Config\Builder;

abstract class PluginTestCase extends TestCase
{
    private const BASE_DIRECTORY = __DIR__ . '/../Environment';

    private static array $configs = [];

    protected function setUp(): void
    {
        self::$configs['params'] = require Builder::path('params', self::BASE_DIRECTORY);
    }

    protected function getParam(string $name)
    {
        return self::$configs['params'][$name];
    }
}
