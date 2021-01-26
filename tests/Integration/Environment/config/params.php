<?php

declare(strict_types=1);

use Yiisoft\Arrays\Collection\ArrayCollection;
use Yiisoft\Arrays\Collection\Modifier\RemoveAllKeys;
use Yiisoft\Arrays\Collection\Modifier\ReplaceValue;
use Yiisoft\Arrays\Collection\Modifier\SaveOrder;
use Yiisoft\Arrays\Collection\Modifier\UnsetValue;
use Yiisoft\Composer\Config\Env;

$objectWithClosures = new stdClass();
$objectWithClosures->closure = function () {
    return 1;
};
$objectWithClosures->staticClosure = static function () {
    return 2;
};
$objectWithClosures->shortClosure = fn () => 3;
$objectWithClosures->staticShortClosure = static fn () => 4;

return new ArrayCollection(
    [
        'boolean parameter' => true,
        'string parameter' => 'value of param 1',
        'NAN parameter' => NAN,
        'var parameter' => $_SERVER,
        'float parameter' => 1.0000001,
        'int parameter' => 123,
        'long int parameter' => 123_000,
        'array parameter' => [
            'changed value' => 'from root config',
            [[[[[]]]]],
        ],
        'array parameter with UnsetArrayValue' => new ArrayCollection(
            [],
            new UnsetValue('first-vendor/first-package'),
        ),
        'array parameter with ReplaceArrayValue' => ['replace'],
        'array parameter with RemoveArrayKeys' => new ArrayCollection([
            'root key' => 'root value',
        ], new RemoveAllKeys()),
        'array parameter with ReverseValues' => new ArrayCollection([
            'root package' => 'root value',
        ], new SaveOrder()),
        'callable parameter' => function () {
            return 'I am callable';
        },
        'static callable parameter' => static function () {
            return 'I am callable';
        },

        'short callable parameter' => fn () => 'I am callable',

        'object parameter' => new stdClass(),

        'env_parameter' => 'default',
        'constant_based_parameter' => TEST_CONSTANT,

        'env.raw' => Env::get('ENV_STRING'),
        'env.raw.default_null' => Env::get('NOT_FOUND', null),
        'env.raw.default_string' => Env::get('NOT_FOUND', 'default value'),
        'env.raw.default_integer' => Env::get('NOT_FOUND', 123),
        'env.raw.default_object' => Env::get('NOT_FOUND', new stdClass()),
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

        'objectWithClosures' => $objectWithClosures,

        __DIR__ . '/relative/path' => __DIR__ . '/relative/path',
    ],
    new ReplaceValue('array parameter with ReplaceArrayValue')
);
