<?php

declare(strict_types=1);

/**
 * @var $params array
 */

use Yiisoft\Composer\Config\Builder;

return [
    \Environment\Serializer\SerializerInterface::class => \Environment\Serializer\PhpSerializer::class,
    'params' => $params,
    'required' => Builder::require('required')
];
