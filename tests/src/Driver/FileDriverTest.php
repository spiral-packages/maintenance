<?php

declare(strict_types=1);

namespace Spiral\Maintenance\Tests\Driver;

use Mockery as m;
use Spiral\Files\FilesInterface;
use Spiral\Maintenance\Driver\FileDriver;
use Spiral\Maintenance\Payload;
use Spiral\Maintenance\PayloadSerializer;
use Spiral\Maintenance\Tests\TestCase;
use Spiral\Serializer\SerializerInterface;
use Spiral\Serializer\SerializerRegistryInterface;

final class FileDriverTest extends TestCase
{
    private FileDriver $driver;
    private SerializerRegistryInterface|m\LegacyMockInterface|m\MockInterface $serializer;
    private m\LegacyMockInterface|FilesInterface|m\MockInterface $files;

    protected function setUp(): void
    {
        parent::setUp();

        $serializer = m::mock(SerializerRegistryInterface::class);
        $serializer->shouldReceive('get')->with('serializer')->andReturn(
            $this->serializer = m::mock(SerializerInterface::class)
        );

        $this->driver = new FileDriver(
            new PayloadSerializer($serializer),
            $this->files = m::mock(FilesInterface::class),
            'dir/'
        );
    }

    public function testActivate(): void
    {
        $payload = new Payload();
        $this->serializer
            ->shouldReceive('serialize')
            ->once()
            ->with($payload)
            ->andReturn('serialized-string');

        $this->files->shouldReceive('write')->once()
            ->with('dir/maintenance', 'serialized-string', null, true);

        $this->driver->activate($payload);
    }

    public function testDeactivate(): void
    {
        $this->files->shouldReceive('delete')->once()
            ->with('dir/maintenance');

        $this->driver->deactivate();
    }

    public function testGetPayload(): void
    {
        $this->files->shouldReceive('exists')->once()->with('dir/maintenance')->andReturnTrue();
        $this->files->shouldReceive('read')
            ->once()
            ->with('dir/maintenance')
            ->andReturn('serialized-string');

        $this->serializer
            ->shouldReceive('unserialize')
            ->once()
            ->with('serialized-string', Payload::class)
            ->andReturn($payload = new Payload());

        $this->assertSame($payload, $this->driver->getPayload());
    }

    public function testGetNonExistPayload(): void
    {
        $this->files->shouldReceive('exists')->once()->with('dir/maintenance')->andReturnFalse();

        $this->assertNull($this->driver->getPayload());
    }
}
