<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Tests\Unit\Util;

use PHPUnit\Framework\TestCase;
use Yiisoft\Composer\Config\Util\Exporter;

final class ExporterTest extends TestCase
{
    /**
     * @dataProvider variablesProvider()
     */
    public function testExportClosure($variable, $exportedVariable): void
    {
        $exportedClosure = Exporter::exportVar($variable);

        $this->assertAccurateToSpaces($exportedVariable, $exportedClosure);
    }

    public function variablesProvider(): array
    {
        $params = ['test' => 42];
        $_ENV['value'] = 1;
        $key = 'test key';

        return [
            [
                $_ENV['value'],
                "1",
            ],
            [
                fn () => $_ENV[$key],
                'fn () => $_ENV[$key]',
            ],
            [
                fn () => $_ENV['value'] ?? null,
                "fn () => \$_ENV['value'] ?? null",
            ],
            [
                fn () => $_ENV['value'] ?? 123,
                "fn () => \$_ENV['value'] ?? 123",
            ],
            [
                fn () => $_ENV['value'] ?? 'string',
                "fn () => \$_ENV['value'] ?? 'string'",
            ],
            [
                fn () => $_ENV['value'] ?? new \stdClass(),
                "fn () => \$_ENV['value'] ?? new \stdClass()",
            ],
            [
                fn () => $params['test'],
                "fn () => \$params['test']",
            ],
            //[
            //    [fn () => $params['test']],
            //    "[fn () => \$params['test']]",
            //],
            [
                static function () use ($params) {
                    return $params['test'];
                },
                "static function () use (\$params) {\n                    return \$params['test'];\n                }",
            ],
            [
                [
                    'function' => static function () use ($params) {
                        return $params['test'];
                    },
                ],
                <<<PHP
[
    'function' => static function () use (\$params) {
                        return \$params['test'];
                    },
]
PHP,
            ],
        ];
    }

    private function assertAccurateToSpaces($expected, $actual, string $message = ''): void
    {
        $expected = preg_replace('/\s+/', " ", $expected);
        $actual = preg_replace('/\s+/', " ", $actual);
        $this->assertSame($expected, $actual, $message);
    }
}
