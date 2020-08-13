<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Tests\Integration\Tests;

class PluginTest extends PluginTestCase
{

    public function testConfigsOrder()
    {
        $this->registerConfig('order-b');
        $this->assertSame(1, $this->getFromConfig('order-b', 'value'));
    }
}
