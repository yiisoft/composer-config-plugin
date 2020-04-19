<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Reader;

interface ReaderInterface
{
    public function read(string $path): array;
}
