<?php

namespace Yiisoft\Composer\Config\Tests\Unit;

use Composer\Composer;
use Composer\Config;
use Yiisoft\Composer\Config\Plugin;

/**
 * Class PluginTest.
 */
class PluginTest extends \PHPUnit\Framework\TestCase
{
    private $object;
    private $io;
    private $composer;
    private $event;
    private $packages = [];

    public function setUp()
    {
        parent::setUp();
        $this->composer = new Composer();
        $this->composer->setConfig(new Config(true, getcwd()));
        $this->io = $this->createMock('Composer\IO\IOInterface');
        $this->event = $this->getMockBuilder('Composer\Script\Event')
            ->setConstructorArgs(['test', $this->composer, $this->io])
            ->getMock();

        $this->object = new Plugin();
        $this->object->setPackages($this->packages);
        $this->object->activate($this->composer, $this->io);
    }

    public function testGetPackages()
    {
        $this->assertSame($this->packages, $this->object->getPackages());
    }

    public function testGetSubscribedEvents()
    {
        $this->assertInternalType('array', $this->object->getSubscribedEvents());
    }
}
