<?php

declare(strict_types=1);

/**
 * @var $params array
 */

return [
    \Environment\Serializer\SerializerInterface::class => \Environment\Serializer\PhpSerializer::class,
    'params' => $params,
    'CustomSerializer' => new \Environment\Serializer\CustomSerializer(fn() => 's', fn() => 'u'),
];
