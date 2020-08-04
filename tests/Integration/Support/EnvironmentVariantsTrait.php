<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Tests\Integration\Support;

trait EnvironmentVariantsTrait
{
    public function getEnvironments()
    {
        return ['composer-extra', 'config-plugin-file', 'mix'];
    }
}