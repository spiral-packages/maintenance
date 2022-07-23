<?php

declare(strict_types=1);

namespace Spiral\Maintenance\Tests\Driver;

use Mockery as m;
use Psr\SimpleCache\CacheInterface;
use Spiral\Cache\CacheStorageProviderInterface;
use Spiral\Cache\Config\CacheConfig;
use Spiral\Maintenance\Driver\CacheDriver;
use Spiral\Maintenance\Payload;
use Spiral\Maintenance\PayloadSerializer;
use Spiral\Maintenance\Tests\TestCase;
use Spiral\Serializer\SerializerInterface;
use Spiral\Serializer\SerializerRegistryInterface;

final class CacheDriverTest extends TestCase
{
    private CacheDriver $driver;
    private SerializerRegistryInterface|m\LegacyMockInterface|m\MockInterface $serializer;
    private m\LegacyMockInterface|m\MockInterface|CacheInterface $cache;

    protected function setUp(): void
    {
        parent::setUp();

        $serializer = m::mock(SerializerRegistryInterface::class);
        $serializer->shouldReceive('get')->with('serializer')->andReturn(
            $this->serializer = m::mock(SerializerInterface::class)
        );

        $provider = m::mock(CacheStorageProviderInterface::class);
        $provider->shouldReceive('storage')->once()->with('foo')->andReturn(
            $this->cache = m::mock(CacheInterface::class)
        );

        $this->driver = new CacheDriver(
            $provider,
            new PayloadSerializer($serializer),
            new CacheConfig([
                'default' => 'foo',
            ]),
            'cache.key'
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

        $this->cache->shouldReceive('set')
            ->once()
            ->with('cache.key', 'serialized-string');

        $this->driver->activate($payload);
    }

    public function testDeactivate(): void
    {
        $this->cache->shouldReceive('delete')->once()->with('cache.key');
        $this->driver->deactivate();
    }

    public function testGetPayload(): void
    {
        $this->cache->shouldReceive('has')->once()->with('cache.key')->andReturnTrue();
        $this->cache->shouldReceive('get')
            ->once()
            ->with('cache.key')
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
        $this->cache->shouldReceive('has')->once()->with('cache.key')->andReturnFalse();

        $this->assertNull($this->driver->getPayload());
    }
}
