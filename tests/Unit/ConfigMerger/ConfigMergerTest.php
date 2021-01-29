<?php

declare(strict_types=1);

namespace ConfigMerger;

use PHPUnit\Framework\TestCase;
use Yiisoft\Composer\Config\ConfigMerger\ConfigMerger;
use Yiisoft\Composer\Config\ConfigMerger\Modifier\InsertValueBeforeKey;
use Yiisoft\Composer\Config\ConfigMerger\Modifier\RemoveKeys;
use Yiisoft\Composer\Config\ConfigMerger\Modifier\ReplaceValue;
use Yiisoft\Composer\Config\ConfigMerger\Modifier\ReverseBlockMerge;
use Yiisoft\Composer\Config\ConfigMerger\Modifier\ReverseValues;
use Yiisoft\Composer\Config\ConfigMerger\Modifier\UnsetValue;

final class ConfigMergerTest extends TestCase
{
    public function testEmptyMerge(): void
    {
        $this->assertEquals([], ConfigMerger::merge(...[]));
    }

    public function testMerge(): void
    {
        $a = [
            'name' => 'Yii',
            'version' => '1.0',
            'options' => [
                'namespace' => false,
                'unittest' => false,
            ],
            'features' => [
                'mvc',
            ],
        ];
        $b = [
            'version' => '1.1',
            'options' => [
                'unittest' => true,
            ],
            'features' => [
                'gii',
            ],
        ];
        $c = [
            'version' => '2.0',
            'options' => [
                'namespace' => true,
            ],
            'features' => [
                'debug',
            ],
            'foo',
        ];

        $result = ConfigMerger::merge($a, $b, $c);
        $expected = [
            'name' => 'Yii',
            'version' => '2.0',
            'options' => [
                'namespace' => true,
                'unittest' => true,
            ],
            'features' => [
                'mvc',
                'gii',
                'debug',
            ],
            'foo',
        ];

        $this->assertEquals($expected, $result);
    }

    public function testMergeWithUnset(): void
    {
        $a = [
            'name' => 'Yii',
            'version' => '1.0',
            'options' => [
                'namespace' => false,
                'unittest' => false,
            ],
            'features' => [
                'mvc',
            ],
        ];
        $b = [
            'version' => '1.1',
            'options' => new UnsetValue(),
            'features' => [
                'gii',
            ],
        ];

        $result = ConfigMerger::merge($a, $b);
        $expected = [
            'name' => 'Yii',
            'version' => '1.1',
            'features' => [
                'mvc',
                'gii',
            ],
        ];

        $this->assertEquals($expected, $result);
    }

    public function testMergeWithReplace(): void
    {
        $a = [
            'name' => 'Yii',
            'version' => '1.0',
            'options' => [
                'namespace' => false,
                'unittest' => false,
            ],
            'features' => [
                'mvc',
            ],
        ];
        $b = [
            'version' => '1.1',
            'options' => [
                'unittest' => true,
            ],
            'features' => new ReplaceValue(
                [
                    'gii',
                ]
            ),
        ];

        $result = ConfigMerger::merge($a, $b);
        $expected = [
            'name' => 'Yii',
            'version' => '1.1',
            'options' => [
                'namespace' => false,
                'unittest' => true,
            ],
            'features' => [
                'gii',
            ],
        ];

        $this->assertEquals($expected, $result);
    }

    public function testMergeWithRemoveKeys(): void
    {
        $a = [
            'name' => 'Yii',
            'version' => '1.0',
        ];
        $b = [
            'version' => '1.1',
            'options' => [],
            new RemoveKeys(),
        ];

        $result = ConfigMerger::merge($a, $b);
        $expected = [
            'Yii',
            '1.1',
            [],
        ];

        $this->assertEquals($expected, $result);
    }

    public function testMergeWithReverseBlock(): void
    {
        $a = [
            'name' => 'Yii',
            'options' => [
                'option1' => 'valueA',
                'option3' => 'valueAA',
            ],
            'version' => '1.0',
            'meta' => ['a' => 1],
        ];
        $b = [
            'version' => '1.1',
            'options' => [
                'option1' => 'valueB',
                'option2' => 'valueBB',
            ],
            ReverseBlockMerge::class => new ReverseBlockMerge(),
        ];

        $result = ConfigMerger::merge($a, $b);
        $expected = [
            'version' => '1.1',
            'options' => [
                'option1' => 'valueB',
                'option2' => 'valueBB',
                'option3' => 'valueAA',
            ],
            'name' => 'Yii',
            'meta' => ['a' => 1],
        ];

        $this->assertSame($expected, $result);
    }

    public function testMergeSimpleArrayWithReverseBlock(): void
    {
        $a = [
            'A',
            'B',
        ];
        $b = [
            'C',
            'B',
            ReverseBlockMerge::class => new ReverseBlockMerge(),
        ];

        $this->assertSame(['C', 'B', 'A'], ConfigMerger::merge($a, $b));
    }

    public function testMergeWithAlmostReverseBlock(): void
    {
        $a = [
            'name' => 'Yii',
            'version' => '1.0',
        ];
        $b = [
            'version' => '1.1',
            ReverseBlockMerge::class => 'hello',
        ];

        $this->assertSame([
            'name' => 'Yii',
            'version' => '1.1',
            ReverseBlockMerge::class => 'hello',
        ], ConfigMerger::merge($a, $b));
    }

    public function testMergeWithReverseValues(): void
    {
        $a = [
            'name' => 'Yii',
            'version' => '1.0',
        ];
        $b = [
            'version' => '1.1',
            'options' => [],
            ReverseValues::class => new ReverseValues(),
        ];

        $result = ConfigMerger::merge($a, $b);
        $expected = [
            'options' => [],
            'version' => '1.1',
            'name' => 'Yii',
        ];

        $this->assertSame($expected, $result);
    }

    public function testMergeWithNullValues(): void
    {
        $a = [
            'firstValue',
            null,
        ];
        $b = [
            'secondValue',
            'thirdValue',
        ];

        $result = ConfigMerger::merge($a, $b);
        $expected = [
            'firstValue',
            null,
            'secondValue',
            'thirdValue',
        ];

        $this->assertEquals($expected, $result);
    }

    public function testMergeIntegerKeyedArraysWithSameValue(): void
    {
        $a = ['2019-01-25'];
        $b = ['2019-01-25'];
        $c = ['2019-01-25'];

        $result = ConfigMerger::merge($a, $b, $c);
        $expected = ['2019-01-25'];

        $this->assertEquals($expected, $result);
    }

    public function testMergeWithInsertValueBeforekey(): void
    {
        $a = [
            'name' => 'Yii',
            'version' => '1.0',
        ];
        $b = [
            'version' => '1.1',
            'options' => [],
            'vendor' => new InsertValueBeforeKey('Yiisoft', 'name'),
        ];

        $result = ConfigMerger::merge($a, $b);
        $expected = [
            'vendor' => 'Yiisoft',
            'name' => 'Yii',
            'version' => '1.1',
            'options' => [],
        ];

        $this->assertSame($expected, $result);
    }

    public function testMergeWithReverseBlockAndIntKeys(): void
    {
        $a = [
            'A',
            'B',
        ];
        $b = [
            'C',
            'D',
            ReverseBlockMerge::class => new ReverseBlockMerge(),
        ];

        $result = ConfigMerger::merge($a, $b);
        $expected = [
            'C',
            'D',
            'A',
            'B',
        ];

        $this->assertSame($expected, $result);
    }
}
