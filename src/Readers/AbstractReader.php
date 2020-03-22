<?php
namespace Yiisoft\Composer\Config\Readers;

use Yiisoft\Composer\Config\Builder;
use Yiisoft\Composer\Config\Exceptions\FailedReadException;

/**
 * Reader - helper to read data from files of different types.
 */
abstract class AbstractReader
{
    /**
     * @var Builder
     */
    protected $builder;

    public function __construct(Builder $builder)
    {
        $this->builder = $builder;
    }

    public function getBuilder(): Builder
    {
        return $this->builder;
    }

    public function read($path): array
    {
        $skippable = 0 === strncmp($path, '?', 1) ? '?' : '';
        if ($skippable) {
            $path = substr($path, 1);
        }

        if (is_readable($path)) {
            $res = $this->readRaw($path);

            return is_array($res) ? $res : [];
        }

        if (empty($skippable)) {
            throw new FailedReadException("failed read file: $path");
        }

        return [];
    }

    public function getFileContents($path): string
    {
        $res = file_get_contents($path);
        if (false === $res) {
            throw new FailedReadException("failed read file: $path");
        }

        return $res;
    }

    abstract public function readRaw($path);
}
