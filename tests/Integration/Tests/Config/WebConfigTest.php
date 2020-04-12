<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Tests\Integration\Tests\Config;

final class WebConfigTest extends ConfigTest
{
    public function configProvider(): array
    {
        return [
            [
                \Environment\Serializer\SerializerInterface::class,
                \Environment\Serializer\PhpSerializer::class,
            ],
        ];
    }

    protected function getConfigName(): string
    {
        return 'web';
    }
}
