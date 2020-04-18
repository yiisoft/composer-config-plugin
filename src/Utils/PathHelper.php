<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Utils;

final class PathHelper
{
    /**
     * PHP implementation of {@see realpath()} method returns `false` when object is not exists.
     * This method prevent this behavior.
     *
     * @param string $path
     * @return string
     */
    public static function realpath(string $path): string
    {
        $parts = explode('/', self::normalize($path));
        $out = [];
        foreach ($parts as $part) {
            if ($part === '.') {
                continue;
            }
            if ($part === '..') {
                array_pop($out);
                continue;
            }
            $out[] = $part;
        }

        return implode('/', $out);
    }

    public static function normalize(string $path): string
    {
        return rtrim(str_replace('//', '/', strtr($path, '/\\', '//')), '/');
    }
}
