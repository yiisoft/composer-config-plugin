<?php

return [
    'config-plugin-output-dir' => 'runtime/build/config',
    'config-plugin' => [
        'constants' => 'config/constants.php',
        'params' => [
            'config/params.php',
            'config/params.yaml',
            'config/params.json',
        ],
        'test' => 'config/test.php',
        'web' => 'config/web.php',
    ],
    'config-plugin-alternatives' => 'config/alternatives.json',
];