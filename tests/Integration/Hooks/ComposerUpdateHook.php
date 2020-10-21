<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Tests\Integration\Hooks;

use PHPUnit\Runner\BeforeFirstTestHook;
use Yiisoft\Composer\Config\Util\PathHelper;

final class ComposerUpdateHook implements BeforeFirstTestHook
{
    public function executeBeforeFirstTest(): void
    {
        $dir = $this->getWorkingDir();
        if (!is_dir("$dir/vendor")) {
            $this->execComposer('update --no-plugins --ignore-platform-reqs --prefer-dist');
        }

        $prj = PathHelper::realpath(dirname(__DIR__, 3));
        $dst = "$dir/vendor/yiisoft/composer-config-plugin";
        exec("rm -rf $dst");
        symlink($prj, $dst);

        $this->execComposer('dump');
    }

    private function execComposer(string $command): void
    {
        $dir = $this->getWorkingDir();
        $this->exec("composer $command -d $dir --no-interaction " . $this->suppressLogs());
    }

    private function exec(string $command): void
    {
        $res = exec($command, $_, $returnCode);
        if ((int) $returnCode !== 0) {
            throw new \RuntimeException("$command return code was $returnCode. $res");
        }
    }

    private function getWorkingDir(): string
    {
        return PathHelper::realpath(dirname(__DIR__)) . '/Environment';
    }

    private function suppressLogs(): string
    {
        $commandArguments = $_SERVER['argv'] ?? [];
        $isDebug = in_array('--debug', $commandArguments, true);

        $tempDir = sys_get_temp_dir();

        return !$isDebug ? "2>{$tempDir}/yiisoft-hook" : '';
    }
}
