<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Tests\Integration\Tests\Config;

include dirname(__DIR__, 3) . '/Integration/Environment/src/Serializer/SerializerInterface.php';
include dirname(__DIR__, 3) . '/Integration/Environment/src/Serializer/PhpSerializer.php';
include dirname(__DIR__, 3) . '/Integration/Environment/src/Serializer/CustomSerializer.php';

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
