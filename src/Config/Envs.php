<?php

namespace Yiisoft\Composer\Config\Config;

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

    public function envsRequired(): bool
    {
        return false;
    }

    public function constantsRequired(): bool
    {
        return false;
    }

    public function paramsRequired(): bool
    {
        return false;
    }
}
