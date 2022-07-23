<?php

declare(strict_types=1);

namespace Spiral\Maintenance\Driver;

use Psr\SimpleCache\CacheInterface;
use Spiral\Cache\CacheStorageProviderInterface;
use Spiral\Cache\Config\CacheConfig;
use Spiral\Maintenance\Payload;
use Spiral\Maintenance\PayloadSerializer;

final class CacheDriver implements DriverInterface
{
    private readonly CacheInterface $cache;

    public function __construct(
        CacheStorageProviderInterface $storageProvider,
        private readonly PayloadSerializer $serializer,
        CacheConfig $cacheConfig,
        private readonly string $key,
        ?string $storage = null
    ) {
        $this->cache = $storageProvider->storage(
            $storage ?? $cacheConfig->getDefaultStorage()
        );
    }

    public function activate(Payload $payload): void
    {
        $this->cache->set(
            $this->key,
            $this->serializer->serialize($payload)
        );
    }

    public function deactivate(): void
    {
        $this->cache->delete($this->key);
    }

    public function getPayload(): ?Payload
    {
        if (! $this->cache->has($this->key)) {
            return null;
        }

        return $this->serializer->unserialize(
            $this->cache->get($this->key)
        );
    }
}
