<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Reader;

/**
 * PhpReader - reads PHP files.
 */
class PhpReader extends AbstractReader implements ReaderWithParamsInterface
{

    private array $params = [];

    protected function readRaw(string $path)
    {
        $config = [];
        foreach ($this->builder->getVars() as $key => $parameters) {
            $config[$key] = $parameters;
        }

        $result = static function (array $params, array $config) {
            return require func_get_arg(2);
        };

        return $result($this->params, $config, $path);
    }

    public function setParams(array $params): void
    {
        $this->params = $params;
    }
}
