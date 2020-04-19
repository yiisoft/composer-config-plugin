<?php

declare(strict_types=1);

use Yiisoft\Arrays\ReplaceArrayValue;
use Yiisoft\Arrays\UnsetArrayValue;
use Yiisoft\Composer\Config\Utils\RemoveArrayKeys;

return [
    'boolean parameter' => true,
    'string parameter' => 'value of param 1',
    'NAN parameter' => NAN,
    'var parameter' => $_SERVER,
    'float parameter' => 1.0000001,
    'int parameter' => 123,
    'long int parameter' => 123_000,
    'array parameter' => [
        'changed value' => 'from root config',
        [[[[[]]]]]
    ],
    'array parameter with UnsetArrayValue' => [
        'first-vendor/first-package' => new UnsetArrayValue(),
    ],
    'array parameter with ReplaceArrayValue' => new ReplaceArrayValue(['replace']),
    'array parameter with RemoveArrayKeys' => [
        'root key' => 'root value',
        new RemoveArrayKeys(),
    ],
    'callable parameter' => function () {
        return 'I am callable';
    },
    'static callable parameter' => static function () {
        return 'I am callable';
    },

    // temporary not working
    // 'short callable parameter' => fn() => 'I am callable',

    'object parameter' => new stdClass(),

    'env_parameter' => 'default',
    'constant_based_parameter' => TEST_CONSTANT,
];
