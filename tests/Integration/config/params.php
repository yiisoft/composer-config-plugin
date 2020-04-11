<?php

declare(strict_types=1);

return [
    'boolean parameter' => true,
    'string parameter' => 'value of param 1',
    'NAN parameter' => NAN,
    'var parameter' => $_SERVER,
    'float parameter' => 1.0000001,
    'int parameter' => 123,
    'long int parameter' => 123_000,
    'array parameter' => [[[[[[]]]]]],
    'callable parameter' => function () {
        return 'I am callable';
    },
    'static callable parameter' => static function () {
        return 'I am callable';
    },

    // temporary not working
    // 'short callable parameter' => fn() => 'I am callable',

    'object parameter' => new stdClass(),
];
