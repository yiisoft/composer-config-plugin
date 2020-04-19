<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config;

use Yiisoft\Composer\Config\Exception\FailedWriteException;

class ContentWriter
{
    public function write(string $path, string $content): void
    {
        if (file_exists($path) && $content === file_get_contents($path)) {
            return;
        }
        $dirname = dirname($path);
        if (!file_exists($dirname) && !mkdir($dirname, 0777, true) && !is_dir($dirname)) {
            throw new FailedWriteException(sprintf('Directory "%s" was not created', $dirname));
        }
        if (false === file_put_contents($path, $content)) {
            throw new FailedWriteException("Failed write file $path");
        }
    }
}
