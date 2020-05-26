<?php

namespace Yiisoft\Composer\Config\Reader;

use Dotenv\Dotenv;
use Yiisoft\Composer\Config\Exception\UnsupportedFileTypeException;

/**
 * EnvReader - reads `.env` files.
 */
class EnvReader extends AbstractReader
{
    protected function readRaw(string $path)
    {
        if (!class_exists(Dotenv::class)) {
            throw new UnsupportedFileTypeException('for .env support require `vlucas/phpdotenv` in your composer.json');
        }
        $info = pathinfo($path);
        Dotenv::createImmutable($info['dirname'], $info['basename'])->load();
        return getenv();
    }
}
