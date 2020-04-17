<?php

namespace Yiisoft\Composer\Config\Config;

/**
 * Rebuild class represents __rebuild.php script.
 */
class Rebuild extends Config
{
    protected function writeFile(string $path, array $data): void
    {
        $this->contentWriter->write($path, file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . '__rebuild.php'));
    }
}
