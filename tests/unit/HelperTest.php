<?php

namespace Yiisoft\Composer\Config\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Yiisoft\Composer\Config\Utils\Helper;
use Yiisoft\Composer\Config\Utils\RemoveArrayKeys;

class HelperTest extends TestCase
{
    public function testExportClosure(): void
    {
        $params = ['test' => 42];
        $closure = static function () use ($params) {
            return $params['test'];
        };

        $exportedClosure = Helper::exportVar($closure);

        $this->assertSameWithoutLE("static function () use (\$params) {\n            return \$params['test'];\n        }", $exportedClosure);
    }

    private function assertSameWithoutLE($expected, $actual, string $message = ''): void
    {
        $expected = preg_replace('/\R/', "\n", $expected);
        $actual = preg_replace('/\R/', "\n", $actual);
        $this->assertSame($expected, $actual, $message);
    }
}
