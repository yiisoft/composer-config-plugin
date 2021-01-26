<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Tests\Unit\Config;

use PHPUnit\Framework\TestCase;
use Yiisoft\Composer\Config\Builder;
use Yiisoft\Composer\Config\Config\ConfigOutput;
use Yiisoft\Composer\Config\Config\ConfigOutputFactory;
use Yiisoft\Composer\Config\Config\Constants;
use Yiisoft\Composer\Config\Config\Params;
use Yiisoft\Composer\Config\Config\System;

/**
 * ConfigFactoryTest.
 */
final class ConfigOutputFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $factory = new ConfigOutputFactory();

        $this->checkCreate($factory, 'common', ConfigOutput::class);
        $this->checkCreate($factory, 'constants', Constants::class);
        $this->checkCreate($factory, 'params', Params::class);
        $this->checkCreate($factory, '__files', System::class);
    }

    public function checkCreate(ConfigOutputFactory $factory, string $name, string $class): void
    {
        $config = $factory->create($this->createMock(Builder::class), $name);
        $this->assertInstanceOf($class, $config);
    }
}
