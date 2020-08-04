<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Tests\Integration\Tests\Config;

final class ConfigPluginFileWebConfigTest extends WebConfigTest
{
    protected function getEnvironmentName(): string
    {
        return 'config-plugin-file';
    }
}
