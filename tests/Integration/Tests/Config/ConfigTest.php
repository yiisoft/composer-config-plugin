<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Tests\Integration\Tests\Config;

use Yiisoft\Composer\Config\Tests\Integration\Tests\PluginTestCase;

abstract class ConfigTest extends PluginTestCase
{
    protected function setUp(): void
    {
        $this->registerConfig($this->getConfigName());
    }

    /**
     * @dataProvider configProvider()
     * @param string $name
     * @param $expectedValue
     */
    public function testConfig(string $name, $expectedValue): void
    {
        $actualValue = $this->getConfigValue($name);
        $this->assertEquals($expectedValue, $actualValue);
    }

    protected function getConfigValue(string $name)
    {
        return $this->getFromConfig($this->getConfigName(), $name);
    }

    abstract protected function getConfigName(): string;
}
