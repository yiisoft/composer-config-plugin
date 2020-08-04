<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Tests\Integration\Tests\Config;

final class MixTestConfigTest extends TestConfigTest
{
    protected function getEnvironmentName(): string
    {
        return 'mix';
    }
}
