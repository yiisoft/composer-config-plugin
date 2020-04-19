<?php

namespace Yiisoft\Composer\Config\Reader;

use Yiisoft\Composer\Config\Builder;
use Yiisoft\Composer\Config\Exception\FailedReadException;

/**
 * Reader - helper to read data from files of different types.
 */
abstract class AbstractReader implements ReaderInterface
{
    protected Builder $builder;

    public function __construct(Builder $builder)
    {
        $this->builder = $builder;
    }

    public function read($path): array
    {
        $skippable = 0 === strncmp($path, '?', 1);
        if ($skippable) {
            $path = substr($path, 1);
        }

        if (is_readable($path)) {
            $res = $this->readRaw($path);

            return is_array($res) ? $res : [];
        }

        if (!$skippable) {
            throw new FailedReadException("failed read file: $path");
        }

        return [];
    }

    protected function getFileContents($path): string
    {
        $res = file_get_contents($path);
        if (false === $res) {
            throw new FailedReadException("failed read file: $path");
        }

        return $res;
    }

    abstract protected function readRaw($path);
}
