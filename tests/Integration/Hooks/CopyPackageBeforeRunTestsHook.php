<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Tests\Integration\Hooks;

use PHPUnit\Runner\BeforeFirstTestHook;
use Yiisoft\Composer\Config\Tests\Integration\Support\DirectoryManipulatorTrait;

final class CopyPackageBeforeRunTestsHook implements BeforeFirstTestHook
{
    use DirectoryManipulatorTrait;

    public function executeBeforeFirstTest(): void
    {
        $composerConfigPluginDirectory = dirname(__DIR__) . '/Packages/yiisoft/';
        $pluginCurrentVersionDirectory = dirname(__DIR__, 3);

        if (file_exists($composerConfigPluginDirectory)) {
            $this->removeDirectoryRecursive($composerConfigPluginDirectory);
        }

        $this->copyDirectory($pluginCurrentVersionDirectory, $composerConfigPluginDirectory);
    }
}
