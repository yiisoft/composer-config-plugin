<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Utils;

final class PathHelper
{
    /**
     * PHP implementation of {@see realpath()} method returns `false` when file or directory does not exist.
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
        $search = ['//', '\\\\', '\\'];
        $replace = ['/', '\\', '/'];
        while (($processedPath = str_replace($search, $replace, $path)) !== $path) {
            $path = $processedPath;
        }

        return rtrim($path, '/');
    }
}
