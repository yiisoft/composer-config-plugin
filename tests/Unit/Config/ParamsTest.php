<?php

namespace Yiisoft\Composer\Config\Tests\Unit\Config;

use PHPUnit\Framework\TestCase;
use Yiisoft\Composer\Config\Config\Params;

/**
 * ParamsTest 
 */
final class ParamsTest extends TestCase
{
    private $newValues = [
    ];

    public function testPushEnvVars(): void
    {
        $res = Params::pushValues([
            'SOME NAME' => null,
            'some.name' => 'old value',
            'some-name' => [
                'key' => 'old value',
            ],
            'some' => [
                'name' => 'old value',
                'dont touch' => null,
            ],
            'some' => [
                'deep' => [
                    'deep' => [
                        'name' => 'old value',
                        'dont touch' => null,
                    ],
                ],
            ],
        ], [
            'SOME_NAME' => 'NEW VALUE',
            'SOME_DEEP_DEEP_NAME' => 'NEW VALUE',
        ]);
        $this->assertEquals([
            'SOME NAME' => null,
            'some.name' => 'NEW VALUE',
            'some-name' => 'NEW VALUE',
            'some' => [
                'name' => 'NEW VALUE',
                'dont touch' => null,
            ],
            'some' => [
                'deep' => [
                    'deep' => [
                        'name' => 'NEW VALUE',
                        'dont touch' => null,
                    ],
                ],
            ],
        ], $res);
    }
}
