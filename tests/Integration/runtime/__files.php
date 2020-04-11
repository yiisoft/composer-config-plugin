<?php

$baseDir = dirname(__DIR__) . '/config/';

return [
    'dotenv' => [],
    'defines' => [],
    'params' => [$baseDir . '/params.php'],
    'web' => [$baseDir . '/web.php'],
    'tests' => [$baseDir . '/web.php'],
];
