<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Tests\Integration;

use PHPUnit\Runner\BeforeFirstTestHook;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Yiisoft\Composer\Config\Util\PathHelper;

final class ComposerUpdateHook implements BeforeFirstTestHook
{
    public function executeBeforeFirstTest(): void
    {
        $originalDirectory = getcwd();
        $newDirectory = PathHelper::realpath(__DIR__) . '/Environment';

        chdir($newDirectory);

        if (is_dir("{$newDirectory}/vendor")) {
            $pluginPath = "{$newDirectory}/vendor/yiisoft/composer-config-plugin";
            if (is_link($pluginPath)) {
                @unlink($pluginPath);
            } elseif (is_dir($pluginPath)) {
                $this->removeDirectoryRecursive($pluginPath);
            }
            symlink("{$newDirectory}/../../../", $pluginPath);
            $command = 'composer dump';
        } else {
            $command = 'composer update -n --prefer-dist --no-progress --ignore-platform-reqs --no-plugins ' . $this->suppressLogs();
        }

        $this->exec($command);

        chdir($originalDirectory);
    }

    private function suppressLogs(): string
    {
        $commandArguments = $_SERVER['argv'] ?? [];
        $isDebug = in_array('--debug', $commandArguments, true);

        $tempDir = sys_get_temp_dir();

        return !$isDebug ? "2>{$tempDir}/yiisoft-hook" : '';
    }

    private function exec(string $command): void
    {
        $res = exec($command, $_, $returnCode);
        if ((int) $returnCode !== 0) {
            throw new \RuntimeException("$command return code was $returnCode. $res");
        }
    }

    private function removeDirectoryRecursive(string $path): void
    {
        $iterator = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS);
        $iterator = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::CHILD_FIRST);

        /* @var \SplFileInfo $file */
        foreach ($iterator as $file) {
            if ($file->isLink() || $file->isFile()) {
                unlink($file->getRealPath());
            } elseif ($file->isDir()) {
                rmdir($file->getRealPath());
            }
        }

        rmdir($path);
    }
}
