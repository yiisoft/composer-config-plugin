<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Readers;

interface ReaderInterface
{
    public function read(string $path): array;
}
