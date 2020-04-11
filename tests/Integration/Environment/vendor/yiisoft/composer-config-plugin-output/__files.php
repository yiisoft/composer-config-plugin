<?php

$projectDir = dirname(__DIR__, 3);
$projectConfigDir = $projectDir . '/config/';
$vendorDir = $projectDir . '/vendor/';

return [
    'dotenv' => [],
    'defines' => [],
    'params' => [
        $projectConfigDir . 'params.php',
        $vendorDir . 'first-vendor/first-package/config/params.php',
        $vendorDir . 'first-vendor/second-package/config/params.php',
        $vendorDir . 'first-dev-vendor/first-package/config/params.php',
        $vendorDir . 'first-dev-vendor/second-package/config/params.php',
        $vendorDir . 'second-vendor/first-package/config/params.php',
        $vendorDir . 'second-vendor/second-package/config/params.php',
        $vendorDir . 'second-dev-vendor/first-package/config/params.php',
        $vendorDir . 'second-dev-vendor/second-package/config/params.php',
    ],
    'web' => [$projectConfigDir . 'web.php'],
    'tests' => [$projectConfigDir . 'web.php'],
];
