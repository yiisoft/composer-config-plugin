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
    private static array $loaders = [];

    private static array $knownReaders = [
        'env' => EnvReader::class,
        'php' => PhpReader::class,
        'json' => JsonReader::class,
        'yaml' => YamlReader::class,
        'yml' => YamlReader::class,
    ];

    public static function get(Builder $builder, string $path, array $params = []): ReaderInterface
    {
        $type = static::detectType($path);
        $class = static::findClass($type);

        $uniqid = $class . ':' . spl_object_hash($builder);
        if (empty(self::$loaders[$uniqid])) {
            self::$loaders[$uniqid] = new $class($builder);
        }

        if (self::$loaders[$uniqid] instanceof ReaderWithParamsInterface) {
            self::$loaders[$uniqid]->setParams($params);
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

    private static function findClass(string $type): string
    {
        if (!array_key_exists($type, static::$knownReaders)) {
            throw new UnsupportedFileTypeException("Unsupported file type: \"$type\"");
        }

        return static::$knownReaders[$type];
    }
}
