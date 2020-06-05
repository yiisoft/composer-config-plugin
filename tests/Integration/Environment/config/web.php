<?php

declare(strict_types=1);

/**
 * @var $params array
 */
$projectDirectoryObject = new stdClass();
$projectDirectoryObject->path = $params['projectDirectory'];

return [
    \Environment\Serializer\SerializerInterface::class => \Environment\Serializer\PhpSerializer::class,
    'params' => $params,
    'projectDirectoryObject' => $projectDirectoryObject,
];
