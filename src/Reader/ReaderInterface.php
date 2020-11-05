<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Reader;

use Yiisoft\Arrays\Collection\ArrayCollection;

interface ReaderInterface
{
    public function read(string $path): ArrayCollection;
}
