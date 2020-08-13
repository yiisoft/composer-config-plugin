<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Tests\Integration\Tests\Config;

final class AnotherConfigTest extends ConfigTest
{

    public function configProvider(): array
    {
        return [
            [
                'params',
                fn(array $params) => $this->assertFalse($params['boolean parameter']),
            ],
        ];
    }

    protected function getDefaultConfigName(): string
    {
        return 'another';
    }
}
