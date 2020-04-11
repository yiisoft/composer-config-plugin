<?php

declare(strict_types=1);

namespace Environment\Serializer;

class PhpSerializer implements SerializerInterface
{
    public function serialize($data): string
    {
        return serialize($data);
    }

    public function unserialize(string $data)
    {
        return unserialize($data);
    }
}
