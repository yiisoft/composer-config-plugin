<?php

$baseDir = dirname(__DIR__, 6);

defined('COMPOSER_CONFIG_PLUGIN_BASEDIR') or define('COMPOSER_CONFIG_PLUGIN_BASEDIR', $baseDir);

return [
    'dotenv' => [],
    'defines' => [],
    'params' => [$baseDir . '/PhpstormProjects/yiisoft/yii-web/config/params.php'],
    'web' => [$baseDir . '/PhpstormProjects/yiisoft/yii-web/config/web.php'],
    'tests' => [$baseDir . '/PhpstormProjects/yiisoft/yii-web/config/web.php'],
];
