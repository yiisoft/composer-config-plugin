<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Config;

use Yiisoft\Composer\Config\Util\Helper;

/**
 * DotEnv class represents output configuration file with ENV values.
 */
class Envs extends Config
{
    protected function writeFile(string $path, array $data): void
    {
        $envs = Helper::exportVar($data);

        $content = <<<PHP
        <?php
        return {$envs};
        PHP;

        $this->contentWriter->write($path, $content . PHP_EOL);
    }

    protected function envsRequired(): bool
    {
        return false;
    }

    protected function constantsRequired(): bool
    {
        return false;
    }

    protected function paramsRequired(): bool
    {
        return false;
    }
}
