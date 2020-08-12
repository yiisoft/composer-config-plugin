<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Tests\Unit\Util;

use PHPUnit\Framework\TestCase;
use Yiisoft\Composer\Config\Util\Helper;

final class HelperTest extends TestCase
{
    /**
     * @dataProvider variablesProvider()
     */
    public function testExportClosure($variable, $exportedVariable): void
    {
        $exportedClosure = Helper::exportVar($variable);

        $this->assertSameWithoutLE($exportedVariable, $exportedClosure);
    }

    public function variablesProvider(): array
    {
        $_ENV['value'] = 1;

        return [
            [
                $_ENV['value'],
                "1",
            ],
        ];
    }

    private function assertSameWithoutLE($expected, $actual, string $message = ''): void
    {
        $expected = preg_replace('/\R/', "\n", $expected);
        $actual = preg_replace('/\R/', "\n", $actual);
        $this->assertSame($expected, $actual, $message);
    }
}
