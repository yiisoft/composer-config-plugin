<?php

namespace Yiisoft\Composer\Config\Tests\Unit\Configs;

use Yiisoft\Composer\Config\Builder;
use Yiisoft\Composer\Config\Configs\Config;
use Yiisoft\Composer\Config\Configs\ConfigFactory;
use Yiisoft\Composer\Config\Configs\Defines;
use Yiisoft\Composer\Config\Configs\Params;
use Yiisoft\Composer\Config\Configs\System;

/**
 * ConfigFactoryTest.
 */
class ConfigFactoryTest extends \PHPUnit\Framework\TestCase
{
    protected $builder;

    public function testCreate()
    {
        $this->builder = new Builder();

        $this->checkCreate('common', Config::class);
        $this->checkCreate('defines', Defines::class);
        $this->checkCreate('params', Params::class);
        $this->checkCreate('__files', System::class);
    }

    public function checkCreate(string $name, string $class)
    {
        $config = ConfigFactory::create($this->builder, $name);
        $this->assertInstanceOf($class, $config);
        $this->assertSame($this->builder, $config->getBuilder());
        $this->assertSame($name, $config->getName());
    }
}
