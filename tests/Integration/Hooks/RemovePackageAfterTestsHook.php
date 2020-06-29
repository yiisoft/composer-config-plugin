<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Tests\Integration\Hooks;

use PHPUnit\Runner\AfterLastTestHook;
use Yiisoft\Composer\Config\Tests\Integration\Support\DirectoryManipulatorTrait;

final class RemovePackageAfterTestsHook implements AfterLastTestHook
{
    use DirectoryManipulatorTrait;

    public function executeAfterLastTest(): void
    {
        $composerConfigPluginDirectory = dirname(__DIR__) . '/Packages/yiisoft/composer-config-plugin/';

        $this->removeDirectoryRecursive($composerConfigPluginDirectory);
    }
}
