<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Reader;

interface ReaderWithParamsInterface
{
    public function setParams(array $params): void;
}
