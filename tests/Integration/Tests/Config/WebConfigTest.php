<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Tests\Integration\Tests\Config;

final class WebConfigTest extends ConfigTest
{
    public function configProvider(): array
    {
        $projectDirectoryObject = new \stdClass();
        $projectDirectoryObject->path = dirname(__DIR__, 2) . '/Environment';

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
                'projectDirectoryObject',
                $projectDirectoryObject,
            ],
        ];
    }

    protected function getDefaultConfigName(): string
    {
        return 'web';
    }
}
