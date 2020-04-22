<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Tests\Integration;

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
        $directory = PathHelper::realpath(__DIR__) . '/Environment';

        echo "Changing CWD to {$directory}" . PHP_EOL;
        chdir($directory);

        require_once 'vendor/autoload.php';
        Builder::rebuild();
    }
}
