<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Tests\Integration\Tests\Config;

final class ConfigPluginFileTestConfigTest extends TestConfigTest
{
    protected function getEnvironmentName(): string
    {
        return 'config-plugin-file';
    }
}
