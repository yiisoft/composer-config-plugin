<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Tests\Integration\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Composer\Config\Builder;

abstract class PluginTestCase extends TestCase
{
    private const BASE_DIRECTORY = __DIR__ . '/../Environment';
    private const OUTPUT_DIRECTORY = self::BASE_DIRECTORY . '/vendor/yiisoft/composer-config-plugin-output';

    final protected function setUp()
    {
        var_dump(self::OUTPUT_DIRECTORY);
        Builder::rebuild(self::OUTPUT_DIRECTORY);

    }

    protected function getParam(string $name)
    {
        $config = require Builder::path('params', self::BASE_DIRECTORY);

        return $config[$name];
    }
}
