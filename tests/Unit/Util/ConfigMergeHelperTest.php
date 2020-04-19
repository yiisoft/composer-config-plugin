<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Tests\Unit\Util;

use PHPUnit\Framework\TestCase;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Arrays\Modifier\ReplaceValue;
use Yiisoft\Arrays\Modifier\UnsetValue;

final class ConfigMergeHelperTest extends TestCase
{
    /**
     * @dataProvider replaceValuesProvider()
     * @dataProvider mergeValuesProvider()
     * @dataProvider replaceArrayValueProvider()
     * @dataProvider unsetArrayValueProvider()
     * @dataProvider emptyValuesProvider()
     * @param array $arraysToMerge
     * @param array $expected
     */
    public function testMerge(array $arraysToMerge, array $expected): void
    {
        $actual = ArrayHelper::merge(...$arraysToMerge);
        $this->assertEquals($expected, $actual);
    }

    public function replaceValuesProvider(): array
    {
        return [
            [
                [
                    [1],
                    [1],
                ],
                [1],
            ],
            [
                [
                    [1, 2, 3],
                    [1, 2, 3],
                ],
                [1, 2, 3],
            ],
            [
                [
                    ['key' => 'value'],
                    ['key' => 'value2'],
                ],
                [
                    'key' => 'value2',
                ],
            ],
        ];
    }

    public function mergeValuesProvider(): array
    {
        return [
            [
                [
                    [1, 2, 3],
                    [4, 5, 6],
                ],
                [1, 2, 3, 4, 5, 6],
            ],
            [
                [
                    [['key' => 'value']],
                    [['key' => 'value2']],
                ],
                [
                    ['key' => 'value'],
                    ['key' => 'value2'],
                ],
            ],
            [
                [
                    ['key' => ['value']],
                    ['key' => ['value2']],
                ],
                [
                    'key' => ['value', 'value2'],
                ],
            ],
        ];
    }

    public function replaceArrayValueProvider(): array
    {
        return [
            [
                [
                    ['key' => ['value']],
                    ['key' => new ReplaceValue('replaced')],
                ],
                [
                    'key' => 'replaced',
                ],
            ],
            [
                [
                    ['key' => ['value']],
                    ['key' => [new ReplaceValue('replaced')]],
                ],
                [
                    'key' => ['replaced'],
                ],
            ],
        ];
    }

    public function unsetArrayValueProvider(): array
    {
        return [
            [
                [
                    ['key' => ['value']],
                    ['key' => new UnsetValue()],
                ],
                [],
            ],
            [
                [
                    ['key' => ['value']],
                    ['key' => [new UnsetValue()]],
                ],
                [
                    'key' => [],
                ],
            ],
        ];
    }

    public function emptyValuesProvider(): array
    {
        return [
            [
                [],
                [],
            ],
            [
                [
                    [],
                ],
                [],
            ],
            [
                [
                    [],
                    [],
                ],
                [],
            ],
        ];
    }
}
