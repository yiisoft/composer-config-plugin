<?php

namespace Yiisoft\Composer\Config\Reader;

use Dotenv\Dotenv;
use Yiisoft\Composer\Config\Exception\UnsupportedFileTypeException;

/**
 * EnvReader - reads `.env` files.
 */
class EnvReader extends AbstractReader
{
    protected function readRaw($path)
    {
        if (!class_exists(Dotenv::class)) {
            throw new UnsupportedFileTypeException('for .env support require `vlucas/phpdotenv` in your composer.json');
        }
        $info = pathinfo($path);
        $this->loadDotenv($info['dirname'], $info['basename']);

        return $_ENV;
    }

    /**
     * Creates and loads Dotenv object.
     * Supports all 2, 3 and 4 version of `phpdotenv`
     * @param mixed $dir
     * @param mixed $file
     */
    private function loadDotenv($dir, $file)
    {
        if (method_exists(Dotenv::class, 'createMutable')) {
            Dotenv::createMutable($dir, $file)->load();
        } elseif (method_exists(Dotenv::class, 'create')) {
            Dotenv::create($dir, $file)->overload();
        } else {
            (new Dotenv($dir, $file))->overload();
        }
    }
}
