<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Tests\Integration;

use PHPUnit\Runner\BeforeFirstTestHook;
use Yiisoft\Composer\Config\Util\PathHelper;

final class ComposerUpdateHook implements BeforeFirstTestHook
{
    public function executeBeforeFirstTest(): void
    {
        $originalDirectory = getcwd();
        $newDirectory = PathHelper::realpath(__DIR__) . '/Environment';

        chdir($newDirectory);

        $this->exec("ls -la");
        $this->exec("pwd");

        if (is_dir("{$newDirectory}/vendor")) {
            @unlink("{$newDirectory}/vendor/yiisoft/composer-config-plugin");
            symlink("{$newDirectory}/../../../", "{$newDirectory}/vendor/yiisoft/composer-config-plugin");
            $command = 'composer dump';
        } else {
            $command = 'composer install -n --prefer-dist --no-progress --ignore-platform-reqs --no-plugins ' . $this->suppressLogs();
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
            throw new \RuntimeException("$command return code was $returnCode. $res" . implode($_));
        }
        print_r($command);
        print_r($res);
        print_r($_);
    }
}
