<?php

namespace Spiral\Maintenance\Tests;

class TestCase extends \Spiral\Testing\TestCase
{
    public function rootDirectory(): string
    {
        return __DIR__.'/../';
    }

    public function defineBootloaders(): array
    {
        return [
            \Spiral\Boot\Bootloader\ConfigurationBootloader::class,
            \Spiral\Cache\Bootloader\CacheBootloader::class,
            \Spiral\Serializer\Bootloader\SerializerBootloader::class,
            \Spiral\Maintenance\Bootloader\MaintenanceBootloader::class,
            // ...
        ];
    }
}
