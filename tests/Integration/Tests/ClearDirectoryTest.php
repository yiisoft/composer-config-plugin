<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Tests\Integration\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Composer\Config\Builder;
use Yiisoft\Files\FileHelper;

class ClearDirectoryTest extends TestCase
{

    public function testClearDirectory(): void
    {
        $baseDir = dirname(__DIR__) . '/ClearDirectoryTestEnvironment';

        FileHelper::removeDirectory($baseDir . '/build');

        Builder::rebuild($baseDir . '/env1');
        Builder::rebuild($baseDir . '/env2');

        $this->assertFileDoesNotExist($baseDir . '/build/config1.php');
        $this->assertFileExists($baseDir . '/build/config2.php');
    }
}
