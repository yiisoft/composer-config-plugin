<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Tests\unit\Util;

use PHPUnit\Framework\TestCase;
use Yiisoft\Composer\Config\Util\PathHelper;

final class PathHelperTest extends TestCase
{
    /**
     * @dataProvider notExistsFilesProvider()
     * @param string $pathToCheck
     * @param string $expected
     */
    public function testRealpath(string $pathToCheck, string $expected): void
    {
        $this->assertEquals($expected, PathHelper::realpath($pathToCheck));
    }

    public function notExistsFilesProvider(): array
    {
        return [
            [
                '/tmp/yii_temp_file',
                '/tmp/yii_temp_file',
            ],
            [
                '/tmp/yii/temp/../../file',
                '/tmp/file',
            ],
            [
                'C:\Temp\Yii\File',
                'C:/Temp/Yii/File',
            ],
            [
                'C:\Temp\Yii\..\..\File',
                'C:/File',
            ],
            [
                'C:\Temp\Yii\../../Temp/Yii/File',
                'C:/Temp/Yii/File',
            ],
        ];
    }

    /**
     * @dataProvider nonNormalizedPathsProvider()
     * @param string $pathToCheck
     * @param string $expected
     */
    public function testNormalize(string $pathToCheck, string $expected): void
    {
        $this->assertEquals($expected, PathHelper::normalize($pathToCheck));
    }

    public function nonNormalizedPathsProvider(): array
    {
        return [
            [
                '/tmp///////////',
                '/tmp',
            ],
            [
                '/tmp////////../',
                '/tmp/..',
            ],
            [
                'C:\\\\\Temp',
                'C:/Temp',
            ],
            [
                'C:\Temp\/File',
                'C:/Temp/File',
            ],
        ];
    }
}
