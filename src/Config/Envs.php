<?php

namespace Yiisoft\Composer\Config\Config;

/**
 * DotEnv class represents output configuration file with ENV values.
 */
class Envs extends Config
{
    protected function writeFile(string $path, array $data): void
    {
        $content = "<?php\n";
        foreach ($data as $key => $value) {
            $content .= sprintf(
                "putenv('%s=%s');\n",
                (string) $key,
                (string) $value
            );
        }

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
