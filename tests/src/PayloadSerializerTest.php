<?php

declare(strict_types=1);

namespace Spiral\Maintenance\Tests;

use Mockery as m;
use Spiral\Maintenance\Payload;
use Spiral\Maintenance\PayloadSerializer;
use Spiral\Serializer\SerializerInterface;
use Spiral\Serializer\SerializerRegistryInterface;

final class PayloadSerializerTest extends TestCase
{
    private SerializerInterface|m\LegacyMockInterface|m\MockInterface $serializer;
    private PayloadSerializer $payloadSerializer;

    protected function setUp(): void
    {
        parent::setUp();

        $registry = m::mock(SerializerRegistryInterface::class);

        $registry->shouldReceive('get')->with('serializer')->andReturn(
            $this->serializer = m::mock(SerializerInterface::class)
        );

        $this->payloadSerializer = new PayloadSerializer($registry);
    }

    public function testSerialize(): void
    {
        $payload = new Payload();
        $this->serializer
            ->shouldReceive('serialize')
            ->once()
            ->with($payload)
            ->andReturn('serialized-string');

        $this->assertSame(
            'serialized-string',
            $this->payloadSerializer->serialize($payload)
        );
    }

    public function testUnserialize(): void
    {
        $payload = new Payload();
        $this->serializer
            ->shouldReceive('unserialize')
            ->once()
            ->with('serialized-string', Payload::class)
            ->andReturn($payload);

        $this->assertSame(
            $payload,
            $this->payloadSerializer->unserialize('serialized-string')
        );
    }
}
