<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Reader;

/**
 * PhpReader - reads PHP files.
 */
class PhpReader extends AbstractReader implements ReaderWithParamsInterface
{
    private $params = [];

    protected function readRaw(string $path)
    {
        $result = static function () {
            /** @noinspection NonSecureExtractUsageInspection */
            $params = func_get_arg(0) ;

            return require func_get_arg(1);
        };

        return $result($this->params, $path);
    }

    public function setParams(array $params): void
    {
        $this->params = $params;
    }
}
