<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Tests\Integration;

use PHPUnit\Runner\BeforeFirstTestHook;
use Yiisoft\Composer\Config\Util\PathHelper;

final class ComposerUpdateHook implements BeforeFirstTestHook
{
    public function executeBeforeFirstTest(): void
    {
        $command = sprintf(
            '%s && %s',
            $this->cwdToEnvironment(),
            '[ -d vendor ] && composer dump || composer update '  ,
        );
        $this->exec($command);
    }

    private function cwdToEnvironment(): string
    {
        return sprintf(
            'cd %s',
            PathHelper::realpath(__DIR__) . '/Environment',
        );
    }

    private function suppressLogs(): string
    {
        $commandArguments = $_SERVER['argv'] ?? [];
        $isDebug = in_array('--debug', $commandArguments, true);

        return !$isDebug ? '2>/dev/null' : '';
    }

    private function exec(string $command): void
    {
        $res = exec($command, $_, $returnCode);
        if ((int) $returnCode !== 0) {
            throw new \RuntimeException("$command return code was $returnCode. $res . ".print_r($_, true ));
        }
    }
}
