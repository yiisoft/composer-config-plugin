<?php

declare(strict_types=1);

namespace Environment\Serializer;

interface SerializerInterface
{
    public function serialize($data): string;

    public function unserialize(string $data);
}
