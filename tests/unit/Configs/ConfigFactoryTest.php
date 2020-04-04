<?php

namespace Yiisoft\Composer\Config\Tests\Unit\Configs;

use PHPUnit\Framework\TestCase;
use Yiisoft\Composer\Config\Builder;
use Yiisoft\Composer\Config\Configs\Config;
use Yiisoft\Composer\Config\Configs\ConfigFactory;
use Yiisoft\Composer\Config\Configs\Constants;
use Yiisoft\Composer\Config\Configs\Params;
use Yiisoft\Composer\Config\Configs\System;

/**
 * ConfigFactoryTest.
 */
final class ConfigFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $factory = new ConfigFactory();

        $this->checkCreate($factory, 'common', Config::class);
        $this->checkCreate($factory, 'constants', Constants::class);
        $this->checkCreate($factory, 'params', Params::class);
        $this->checkCreate($factory, '__files', System::class);
    }

    public function checkCreate(ConfigFactory $configFactory, string $name, string $class): void
    {
        $config = $configFactory->create($this->createMock(Builder::class), $name);
        $this->assertInstanceOf($class, $config);
    }
}
