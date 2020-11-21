<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Tests\Integration\Tests\Config;

use Closure;
use Yiisoft\Composer\Config\Tests\Integration\Tests\Helper\LiterallyCallback;
use Yiisoft\Composer\Config\Tests\Integration\Tests\PluginTestCase;

abstract class ConfigTest extends PluginTestCase
{
    protected function setUp(): void
    {
        $this->registerConfig($this->getDefaultConfigName());
    }

    /**
     * @dataProvider configProvider()
     *
     * @param string $name
     * @param $expectedValue
     * @param string|null $configName
     */
    public function testConfig(string $name, $expectedValue, string $configName = null): void
    {
        $actualValue = $this->getConfigValue($name, $configName);
        if ($expectedValue instanceof Closure) {
            $expectedValue($actualValue);
            return;
        }
        if ($expectedValue instanceof LiterallyCallback) {
            $expectedValue = $expectedValue->getCallback();
        }

        $this->assertEquals($expectedValue, $actualValue);
    }

    protected function getConfigValue(string $name, string $configName = null)
    {
        if ($configName !== null) {
            $this->registerConfig($configName);
        }
        return $this->getFromConfig($configName ?? $this->getDefaultConfigName(), $name);
    }

    abstract protected function getDefaultConfigName(): string;
}
