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
                'required',
                [
                    'question' => 'The Ultimate Question of Life, The Universe, and Everything',
                    'answer' => 42,
                ],
            ],
        ];
    }

    protected function getDefaultConfigName(): string
    {
        return 'web';
    }
}
