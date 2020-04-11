<?php

declare(strict_types=1);

namespace Environment\Tests\Serializer;

use Environment\Serializer\SerializerInterface;

class TestSerializer implements SerializerInterface
{
    public function serialize($data): string
    {
    }

    public function unserialize(string $data)
    {
    }
}
