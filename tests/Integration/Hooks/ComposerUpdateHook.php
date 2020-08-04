<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Tests\Integration\Hooks;

use PHPUnit\Runner\BeforeFirstTestHook;
use Yiisoft\Composer\Config\Tests\Integration\Support\DirectoryManipulatorTrait;
use Yiisoft\Composer\Config\Tests\Integration\Support\EnvironmentVariantsTrait;
use Yiisoft\Composer\Config\Util\PathHelper;

final class ComposerUpdateHook implements BeforeFirstTestHook
{
    use DirectoryManipulatorTrait;
    use EnvironmentVariantsTrait;

    public function executeBeforeFirstTest(): void
    {
        $originalDirectory = getcwd();
        $baseDirectory = PathHelper::realpath(dirname(__DIR__)) . '/Environments';
        $templateDirectory = PathHelper::realpath(dirname(__DIR__)) . '/EnvironmentTemplate';

        foreach ($this->getEnvironments() as $environmentName) {
            $newDirectory = $baseDirectory . '/' . $environmentName;
            
            chdir($newDirectory);
    
            if (is_dir("{$newDirectory}/vendor")) {
                $pluginPath = "{$newDirectory}/vendor/yiisoft/composer-config-plugin";
                if (is_link($pluginPath)) {
                    $this->unlink($pluginPath);
                } elseif (is_dir($pluginPath)) {
                    $this->removeDirectoryRecursive($pluginPath);
                }
                symlink("{$newDirectory}/../../../", $pluginPath);
                $command = 'composer dump';
            } else {
                // build environment 
                $this->copyDirectory($templateDirectory, $newDirectory);
                $command = 'composer update -n --prefer-dist --no-progress --ignore-platform-reqs --no-plugins ' . $this->suppressLogs();
            }
    
            $this->exec($command);
        }


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

    public function unlink(string $path): bool
    {
        $isWindows = DIRECTORY_SEPARATOR === '\\';

        if (!$isWindows) {
            return unlink($path);
        }

        if (is_link($path) && is_dir($path)) {
            return rmdir($path);
        }

        return unlink($path);
    }
}
