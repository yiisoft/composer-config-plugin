<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Tests\Integration;

use PHPUnit\Runner\BeforeFirstTestHook;

class ComposerUpdateHook implements BeforeFirstTestHook
{
    public function executeBeforeFirstTest(): void
    {
        $command = sprintf(
            'cd %s && %s',
            __DIR__ . '/Environment',
            'composer upd -n --no-progress --no-suggest --ignore-platform-reqs 2>/dev/null'
        );
        exec($command);
    }
}
