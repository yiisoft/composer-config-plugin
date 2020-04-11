<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Tests\Integration;

use Composer\Factory;
use Composer\IO\IOInterface;
use PHPUnit\Framework\TestCase;

class PluginTest extends TestCase
{
    public function testMain()
    {
        $io = $this->createMock(IOInterface::class);
        $factory = new Factory();
        $currentDir = __DIR__;
        $composer = $factory->createComposer($io, $currentDir . '/composer.json', false, $currentDir, true);


        var_dump($composer->getPackage()->getName());
    }
}
