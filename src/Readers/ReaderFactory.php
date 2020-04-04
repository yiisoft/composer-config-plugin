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

    public static function get(Builder $builder, $path): ReaderInterface
    {
        $type = static::detectType($path);
        $class = static::findClass($type);

        if (!array_key_exists($class, self::$loaders)) {
            self::$loaders[$class] = static::create($builder, $type);
        }

        return self::$loaders[$class];
    }

    public static function detectType($path): string
    {
        if (strncmp(basename($path), '.env.', 5) === 0) {
            return 'env';
        }

        return pathinfo($path, PATHINFO_EXTENSION);
    }

    public static function findClass(string $type): string
    {
        if (empty(static::$knownReaders[$type])) {
            throw new UnsupportedFileTypeException("unsupported file type: $type");
        }

        return static::$knownReaders[$type];
    }

    public static function create(Builder $builder, $type)
    {
        $class = static::findClass($type);

        return new $class($builder);
    }
}
