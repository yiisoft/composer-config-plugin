<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Tests\Integration\Hooks;

use PHPUnit\Runner\BeforeFirstTestHook;
use Yiisoft\Composer\Config\Tests\Integration\Support\EnvironmentVariantsTrait;
use Yiisoft\Composer\Config\Util\PathHelper;

final class RebuildHook implements BeforeFirstTestHook
{
    use EnvironmentVariantsTrait;

    public function executeBeforeFirstTest(): void
    {
        if (!(bool) ($_SERVER['REBUILD'] ?? false)) {
            return;
        }
        $baseDir = PathHelper::realpath(dirname(__DIR__)) . '/Environments';

        foreach ($this->getEnvironments() as $environmentName) {
            $environmentDir = $baseDir . '/' . $environmentName;

            $cmd = "php -r \"require_once '{$environmentDir}/vendor/autoload.php';" .
                   "\Yiisoft\Composer\Config\Builder::rebuild('{$environmentDir}');\"";

            shell_exec(escapeshellcmd($cmd));
        }
    }
}
