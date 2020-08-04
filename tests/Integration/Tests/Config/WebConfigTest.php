<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Tests\Integration\Tests\Config;

abstract class WebConfigTest extends ConfigTest
{
    public function configProvider(): array
    {
        return [
            [
                \Environment\Serializer\SerializerInterface::class,
                \Environment\Serializer\PhpSerializer::class,
            ],
            [
                'params',
                fn (array $params) => $this->assertSame('default', $params['env_parameter']),
            ],
        ];
    }

    protected function getDefaultConfigName(): string
    {
        return 'web';
    }
}
