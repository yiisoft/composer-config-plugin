<?php

declare(strict_types=1);

/**
 * @var $params array
 */

return [
    \Environment\Serializer\SerializerInterface::class => \Environment\Serializer\PhpSerializer::class,
    'params' => $params,

    \Environment\Serializer\CustomSerializer::class => new \Environment\Serializer\CustomSerializer(
        fn () => 'serialize',
        fn () => 'unserialize',
    ),
];
