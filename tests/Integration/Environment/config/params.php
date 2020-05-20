<?php

declare(strict_types=1);

use Yiisoft\Arrays\Modifier\RemoveKeys;
use Yiisoft\Arrays\Modifier\ReplaceValue;
use Yiisoft\Arrays\Modifier\UnsetValue;
use Yiisoft\Composer\Config\Env;

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
        'first-vendor/first-package' => new UnsetValue(),
    ],
    'array parameter with ReplaceArrayValue' => new ReplaceValue(['replace']),
    'array parameter with RemoveArrayKeys' => [
        'root key' => 'root value',
        new RemoveKeys(),
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

    'env.raw' => Env::get('ENV_STRING'),
    'env.string' => 'old value',
    'env.number' => 'old value',
    'env.text' => 'old value',
    'env.params' => [
        'first' => 'old value',
        'deeper' => [
            'second' => 'old value',
            'and' => [
                'deepest' => 'old value',
            ],
        ],
    ],

    'parameters from .env' => [
        'ENV_STRING' => $_ENV['ENV_STRING'] ?? null,
        'ENV_NUMBER' => $_ENV['ENV_NUMBER'] ?? null,
        'ENV_TEXT' => $_ENV['ENV_TEXT'] ?? null,
    ],

    'parameters from .env through constants' => [
        'ENV_STRING' => ENV_STRING,
        'ENV_NUMBER' => ENV_NUMBER,
        'ENV_TEXT' => ENV_TEXT,
    ],
];
