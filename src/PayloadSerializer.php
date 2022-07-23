<?php

declare(strict_types=1);

namespace Spiral\Maintenance;

use Spiral\Serializer\SerializerInterface;
use Spiral\Serializer\SerializerRegistryInterface;

final class PayloadSerializer
{
    private readonly SerializerInterface $serializer;

    public function __construct(
        SerializerRegistryInterface $registry
    ) {
        $this->serializer = $registry->get('serializer');
    }

    public function serialize(Payload $payload): string
    {
        return (string) $this->serializer->serialize($payload);
    }

    public function unserialize(string $payload): Payload
    {
        return $this->serializer->unserialize($payload, Payload::class);
    }
}
