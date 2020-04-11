<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Tests\Integration\Tests;

final class ParamsTest extends PluginTestCase
{
    /**
     * @dataProvider parametersProvider()
     * @param string $name
     * @param $expectedValue
     */
    public function testParameters(string $name, $expectedValue): void
    {
        $actualValue = $this->getParam($name);
        $this->assertEquals($expectedValue, $expectedValue);
    }

    public function parametersProvider(): array
    {
        return [
            ['boolean parameter', true],
            ['string parameter', 'value of param 1'],
            ['NAN parameter', NAN],
            ['var parameter', $_SERVER],
            ['float parameter', 1.0000001],
            ['int parameter', 123],
            ['long int parameter', 123_000],
            ['array parameter', [[[[[[]]]]]]],
        ];
    }
}
