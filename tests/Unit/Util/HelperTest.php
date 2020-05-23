<?php

namespace Yiisoft\Composer\Config\Tests\Unit\Util;

use PHPUnit\Framework\TestCase;
use Yiisoft\Composer\Config\Env;
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
        $params = ['test' => 42];
        $_ENV['value'] = 1;
        $key = 'test key';

        return [
            [
                Env::get('value'),
                "\$_ENV['value']",
            ],
            [
                Env::get('value', null),
                "\$_ENV['value'] ?? null",
            ],
            [
                Env::get('value', 'string'),
                "\$_ENV['value'] ?? 'string'",
            ],
            [
                Env::get('value', 123),
                "\$_ENV['value'] ?? 123",
            ],
            [
                Env::get('value', new \stdClass()),
                "\$_ENV['value'] ?? unserialize('O:8:\"stdClass\":0:{}')",
            ],
            [
                $_ENV['value'],
                "1",
            ],
            [
                fn() => $_ENV[$key],
                'fn() => $_ENV[$key]',
            ],
            [
                fn() => $params['test'],
                "fn() => \$params['test']",
            ],
            [
                [fn() => $params['test']],
                "[fn() => \$params['test']]",
            ],
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

    private function assertSameWithoutLE($expected, $actual, string $message = ''): void
    {
        $expected = preg_replace('/\R/', "\n", $expected);
        $actual = preg_replace('/\R/', "\n", $actual);
        $this->assertSame($expected, $actual, $message);
    }
}
