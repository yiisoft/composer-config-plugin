<?php

namespace Yiisoft\Composer\Config\Configs;

use Yiisoft\Composer\Config\Utils\Helper;

use Yiisoft\Composer\Config\Utils\Helper;

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
