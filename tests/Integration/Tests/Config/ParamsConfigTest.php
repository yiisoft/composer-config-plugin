<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Tests\Integration\Tests\Config;

use stdClass;
use Yiisoft\Composer\Config\Tests\Integration\Tests\Helper\LiterallyCallback;

final class ParamsConfigTest extends ConfigTest
{
    public function configProvider(): array
    {
        return [
            ...$this->phpFilesProvider(),
            ...$this->yamlFilesProvider(),
        ];
    }

    protected function getDefaultConfigName(): string
    {
        return 'params';
    }

    private function phpFilesProvider(): array
    {
        return [
            ['boolean parameter', true],
            ['string parameter', 'value of param 1'],
            ['NAN parameter', 'NAN'],
            ['float parameter', 1.0000001],
            ['int parameter', 123],
            ['long int parameter', 123_000],
            ['array parameter', [[[[[[]]]]]]],
            [
                'callable parameter',
                new LiterallyCallback(function () {
                    return 'I am callable';
                }),
            ],
            [
                'static callable parameter',
                new LiterallyCallback(static function () {
                    return 'I am callable';
                }),
            ],
            ['object parameter', new stdClass()],
            /**
             * Test for subpackages parameters
             */
            ['first-vendor/first-package', true],
            ['first-vendor/second-package', true],
            ['first-dev-vendor/first-package', true],
            ['first-dev-vendor/second-package', true],
            ['second-vendor/first-package', true],
            ['second-vendor/second-package', true],
            ['second-dev-vendor/first-package', true],
            ['second-dev-vendor/second-package', true],
            ['constant_based_parameter', 'a constant value defined in config/constants.php'],
            ['constant_from_vendor', 'a constant value defined in first-dev-vendor/second-package'],
        ];
    }

    private function yamlFilesProvider(): array
    {
        return [
            [
                'parameters',
                [
                    'param1' => 'value1',
                    'param2' => true,
                    'param3' => [1, 'value2'],
                ],
            ],
        ];
    }
}
