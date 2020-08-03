<?php

declare(strict_types=1);

use Yiisoft\Composer\Config\Command\RebuildCommand;

return [
    'yiisoft/yii-console' => [
        'commands' => [
            'config/rebuild' => RebuildCommand::class,
        ]
    ],
];
