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
            [
                'params',
                fn (array $params) => $this->assertSame('default', $params['env_parameter']),
            ],
            [
                \Environment\Serializer\CustomSerializer::class,
                new \Environment\Serializer\CustomSerializer(
                    fn () => 'serialize',
                    fn () => 'unserialize',
                ),
            ],
        ];
    }

    protected function getDefaultConfigName(): string
    {
        return 'web';
    }
}
