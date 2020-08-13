<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Tests\Integration\Tests\Config;

final class SecondPackageConfigTest extends ConfigTest
{

    public function configProvider(): array
    {
        return [
            [
                'params',
                fn(array $params) => $this->assertFalse($params['value']),
            ],
        ];
    }

    protected function getDefaultConfigName(): string
    {
        return 'second';
    }
}
