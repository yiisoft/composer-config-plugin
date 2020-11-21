<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Reader;

use Yiisoft\Composer\Config\Builder;
use Yiisoft\Composer\Config\Exception\UnsupportedFileTypeException;

/**
 * Reader - helper to load data from files of different types.
 */
class ReaderFactory
{
    /** @psalm-var array<string, ReaderInterface> */
    private static array $loaders = [];

    private static array $knownReaders = [
        'env' => EnvReader::class,
        'php' => PhpReader::class,
        'json' => JsonReader::class,
        'yaml' => YamlReader::class,
        'yml' => YamlReader::class,
    ];

    public static function get(Builder $builder, string $path): ReaderInterface
    {
        $class = static::findClass($path);

        $uniqid = $class . ':' . spl_object_hash($builder);
        if (empty(self::$loaders[$uniqid])) {
            /** @psalm-var ReaderInterface */
            self::$loaders[$uniqid] = new $class($builder);
        }

        /** @psalm-var ReaderInterface */
        return self::$loaders[$uniqid];
    }

    private static function detectType(string $path): string
    {
        if (strncmp(basename($path), '.env.', 5) === 0) {
            return 'env';
        }

        return pathinfo($path, PATHINFO_EXTENSION);
    }

    private static function findClass(string $path): string
    {
        $type = static::detectType($path);
        if (!array_key_exists($type, static::$knownReaders)) {
            throw new UnsupportedFileTypeException("Unsupported file type for \"$path\".");
        }

        return static::$knownReaders[$type];
    }
}
