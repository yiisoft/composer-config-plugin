<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Tests\Integration;

use PHPUnit\Runner\BeforeFirstTestHook;
use Yiisoft\Composer\Config\Util\PathHelper;

final class ComposerUpdateHook implements BeforeFirstTestHook
{
    public function executeBeforeFirstTest(): void
    {
        $originalWD = getcwd();
        $newWD = PathHelper::realpath(__DIR__) . '/Environment';

        chdir($newWD);



        $command = sprintf(
            ' composer update '
        );
        $this->exec($command);
        chdir($originalWD);
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
