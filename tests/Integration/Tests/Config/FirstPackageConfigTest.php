<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Tests\Integration\Tests\Config;

final class FirstPackageConfigTest extends ConfigTest
{

    public function configProvider(): array
    {
        return [
            [
                'params',
                fn(array $params) => $this->assertTrue($params['value']),
            ],
        ];
    }

    protected function getDefaultConfigName(): string
    {
        return 'first';
    }
}
