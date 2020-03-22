<?php

namespace Yiisoft\Composer\Config\Readers;

/**
 * PhpReader - reads PHP files.
 */
class PhpReader extends AbstractReader
{
    public function readRaw($__path)
    {
        /// Expose variables to be used in configs
        extract($this->builder->getVars());

        return require $__path;
    }
}
