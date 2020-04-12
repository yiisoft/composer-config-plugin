<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Tests\Integration;

use PHPUnit\Runner\BeforeFirstTestHook;

final class ComposerUpdateHook implements BeforeFirstTestHook
{
    public function executeBeforeFirstTest(): void
    {
        $commandArguments = $_SERVER['argv'] ?? [];
        $isDebug = in_array('--debug', $commandArguments, true);
        $hideLogs = !$isDebug ? '2>/dev/null' : '';
        $command = sprintf(
            'cd %s && %s && %s',
            __DIR__ . '/Environment',
            'rm vendor -rf ' . $hideLogs,
            'composer upd -n --prefer-dist --no-progress --no-suggest --ignore-platform-reqs ' . $hideLogs
        );

        $res = exec($command, $_, $returnCode);

        if ((int) $returnCode !== 0) {
            throw new \RuntimeException($res);
        }
    }
}
