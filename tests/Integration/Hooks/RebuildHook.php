<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Tests\Integration\Hooks;

use PHPUnit\Runner\BeforeFirstTestHook;
use Yiisoft\Composer\Config\Builder;
use Yiisoft\Composer\Config\Util\PathHelper;

final class RebuildHook implements BeforeFirstTestHook
{
    public function executeBeforeFirstTest(): void
    {
        if (!(bool) ($_SERVER['REBUILD'] ?? false)) {
            return;
        }
        $baseDir = PathHelper::realpath(__DIR__) . '/Environment';

        require_once $baseDir . '/vendor/autoload.php';
        echo 'Rebuild configs...' . PHP_EOL;
        Builder::rebuild($baseDir);
    }
}
