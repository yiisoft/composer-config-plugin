<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Tests\Integration\Tests\Config;

abstract class TestConfigTest extends ConfigTest
{
    public function configProvider(): array
    {
        return [
            [
                \Environment\Serializer\SerializerInterface::class,
                \Environment\Tests\Serializer\TestSerializer::class,
            ],
        ];
    }

    abstract protected function getEnvironmentName(): string;

    protected function getDefaultConfigName(): string
    {
        return 'test';
    }
}
