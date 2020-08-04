<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Tests\Integration\Tests\Config;

abstract class AlternativesTest extends ConfigTest
{
    public function configProvider(): array
    {
        return [
            [
                'env_parameter',
                fn (string $param) => $this->assertSame('yiiframework.com', $param),
                'yiiframework.com/params'
            ],
            [
                'params',
                fn (array $params) => $this->assertSame('yiiframework.com', $params['env_parameter']),
                'yiiframework.com/web'
            ],
            [
                'params',
                fn (array $params) => $this->assertSame('beta.yiiframework.ru', $params['env_parameter']),
                'yiiframework.ru/web'
            ],
        ];
    }

    abstract protected function getEnvironmentName(): string;

    protected function getDefaultConfigName(): string
    {
        return 'web';
    }
}
