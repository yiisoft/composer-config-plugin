<?php

declare(strict_types=1);

namespace Environment\Serializer;

class CustomSerializer implements SerializerInterface
{
    private \Closure $serialize;

    private \Closure $unserialize;

    public function __construct(\Closure $serialize, \Closure $unserialize)
    {
        $this->serialize = $serialize;
        $this->unserialize = $unserialize;
    }

    public function serialize($data): string
    {
        return ($this->serialize)($data)();
    }

    public function unserialize(string $data)
    {
        return ($this->unserialize)($data)();
    }
}
