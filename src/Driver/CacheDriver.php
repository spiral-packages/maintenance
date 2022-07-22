<?php

declare(strict_types=1);

namespace Spiral\Maintenance\Driver;

use Psr\SimpleCache\CacheInterface;
use Spiral\Cache\CacheStorageProviderInterface;
use Spiral\Cache\Config\CacheConfig;
use Spiral\Maintenance\Payload;
use Spiral\Serializer\SerializerInterface;

final class CacheDriver implements DriverInterface
{
    private readonly string $storage;

    public function __construct(
        private readonly CacheStorageProviderInterface $storageProvider,
        private readonly SerializerInterface $serializer,
        CacheConfig $cacheConfig,
        private readonly string $key,
        ?string $storage = null
    ) {
        $this->storage = $storage ?: $cacheConfig->getDefaultStorage();
    }

    public function activate(Payload $payload): void
    {
        $this->getCache()->set(
            $this->key,
            $this->serializer->serialize($payload)
        );
    }

    public function deactivate(): void
    {
        $this->getCache()->delete($this->key);
    }

    public function getPayload(): ?Payload
    {
        if (! $this->getCache()->has($this->key)) {
            return null;
        }

        return $this->serializer->unserialize(
            $this->getCache()->get($this->key),
            Payload::class
        );
    }

    private function getCache(): CacheInterface
    {
        return $this->storageProvider->storage($this->storage);
    }
}
