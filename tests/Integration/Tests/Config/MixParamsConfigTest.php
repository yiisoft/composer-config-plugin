<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Tests\Integration\Tests\Config;

final class MixParamsConfigTest extends ParamsConfigTest
{
    protected function getEnvironmentName(): string
    {
        return 'mix';
    }
}
