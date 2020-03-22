<?php

namespace Yiisoft\Composer\Config\Tests\Unit;

use Composer\Composer;
use Composer\Config;
use PHPUnit\Framework\TestCase;
use Yiisoft\Composer\Config\Plugin;
use Composer\IO\IOInterface;

/**
 * Class PluginTest.
 */
class PluginTest extends TestCase
{
    private $object;
    private $packages = [];

    public function setUp()
    {
        parent::setUp();
        $composer = new Composer();
        $composer->setConfig(new Config(true, getcwd()));
        $io = $this->createMock(IOInterface::class);

        $this->object = new Plugin();
        $this->object->setPackages($this->packages);
        $this->object->activate($composer, $io);
    }

    public function testGetPackages(): void
    {
        $this->assertSame($this->packages, $this->object->getPackages());
    }

    public function testGetSubscribedEvents(): void
    {
        $this->assertInternalType('array', $this->object->getSubscribedEvents());
    }
}
