<?php

namespace Yiisoft\Composer\Config\Readers;

use Yiisoft\Composer\Config\Builder;
use Yiisoft\Composer\Config\Exceptions\UnsupportedFileTypeException;

/**
 * Reader - helper to load data from files of different types.
 */
class ReaderFactory
{
    private static array $loaders = [];

    private static array $knownReaders = [
        'env' => EnvReader::class,
        'php' => PhpReader::class,
        'json' => JsonReader::class,
        'yaml' => YamlReader::class,
        'yml' => YamlReader::class,
    ];

    private Builder $builder;

    public function __construct(Builder $builder)
    {
        $this->builder = $builder;
    }

    public static function get(Builder $builder, string $path): ReaderInterface
    {
        $type = static::detectType($path);
        $class = static::findClass($type);

        $uniqid = $class . ':' . spl_object_hash($builder);
        if (empty(self::$loaders[$uniqid])) {
            self::$loaders[$uniqid] = static::create($builder, $type);
        }

        return self::$loaders[$uniqid];
    }

    private static function detectType(string $path): string
    {
        if (strncmp(basename($path), '.env.', 5) === 0) {
            return 'env';
        }

        return pathinfo($path, PATHINFO_EXTENSION);
    }

    private static function create(Builder $builder, string $type): ReaderInterface
    {
        $class = static::findClass($type);

        return new $class($builder);
    }

    private static function findClass(string $type): string
    {
        if (!array_key_exists($type, static::$knownReaders)) {
            throw new UnsupportedFileTypeException("Unsupported file type: \"$type\"");
        }

        return static::$knownReaders[$type];
    }
}
