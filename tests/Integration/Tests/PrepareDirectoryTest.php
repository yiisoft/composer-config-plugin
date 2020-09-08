<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Tests\Integration\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Composer\Config\Builder;
use Yiisoft\Files\FileHelper;

class PrepareDirectoryTest extends TestCase
{

    private const BASE_DIRECTORY = __DIR__ . '/../PrepareDirectoryTestEnvironment';

    public function testClearDirectory(): void
    {
        FileHelper::removeDirectory(self::BASE_DIRECTORY . '/build');

        Builder::rebuild(self::BASE_DIRECTORY . '/env1');
        Builder::rebuild(self::BASE_DIRECTORY . '/env2');

        $this->assertFileDoesNotExist(self::BASE_DIRECTORY . '/build/config1.php');
        $this->assertFileExists(self::BASE_DIRECTORY . '/build/config2.php');
    }
}
