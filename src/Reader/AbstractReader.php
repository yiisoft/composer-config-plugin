<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Reader;

use Yiisoft\Arrays\Collection\ArrayCollection;
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

    public function read($path): ArrayCollection
    {
        $collection = new ArrayCollection();

        $skippable = 0 === strncmp($path, '?', 1);
        if ($skippable) {
            $path = substr($path, 1);
        }

        if (is_readable($path)) {
            $res = $this->readRaw($path);

            return is_array($res) || $res instanceof ArrayCollection
                ? $collection->mergeWith($res)
                : $collection;
        }

        if (!$skippable) {
            throw new FailedReadException("Failed read file: $path");
        }

        return $collection;
    }

    protected function getFileContents(string $path): string
    {
        $res = file_get_contents($path);
        if (false === $res) {
            throw new FailedReadException("Failed read file: $path");
        }

        return $res;
    }

    abstract protected function readRaw(string $path);
}
