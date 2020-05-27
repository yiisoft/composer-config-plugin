<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Tests\Integration;

use PHPUnit\Runner\BeforeFirstTestHook;
use Yiisoft\Composer\Config\Util\PathHelper;

final class ComposerUpdateHook implements BeforeFirstTestHook
{
    public function executeBeforeFirstTest(): void
    {
        $originDir = getcwd();
        $newDir = PathHelper::realpath(__DIR__) . '/Environment';

        chdir($newDir);

        if (is_dir("{$newDir}/vendor")) {
            @unlink("{$newDir}/vendor/yiisoft/composer-config-plugin");
            symlink("{$newDir}/../../../", "{$newDir}/vendor/yiisoft/composer-config-plugin");
            $command = 'composer dump';
        } else {
            $command = 'composer update -n --prefer-dist --no-progress --ignore-platform-reqs --no-plugins ' . $this->suppressLogs();
        }

        $this->exec($command);

        chdir($originDir);
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
}
