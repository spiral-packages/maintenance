<?php

declare(strict_types=1);

namespace Spiral\Maintenance\Tests\Bootloader;

use Spiral\Maintenance\Config\MaintenanceConfig;
use Spiral\Maintenance\Driver\CacheDriver;
use Spiral\Maintenance\Driver\DriverInterface;
use Spiral\Maintenance\Driver\FileDriver;
use Spiral\Maintenance\DriverManager;
use Spiral\Maintenance\Tests\TestCase;

final class MaintenanceBootloaderTest extends TestCase
{
    public const ENV = [
        'MAINTENANCE_DRIVER' => 'cache'
    ];

    public function testDriverManagerBinding(): void
    {
        $this->assertContainerBoundAsSingleton(
            DriverManager::class,
            DriverManager::class
        );
    }

    public function testDriverInterfaceBinding(): void
    {
        $this->assertContainerBoundAsSingleton(
            DriverInterface::class,
            CacheDriver::class
        );
    }

    public function testConfig(): void
    {
        $config = $this->getConfig(MaintenanceConfig::CONFIG);

        $this->assertSame('cache', $config['default']);

        $this->assertSame(FileDriver::class, $config['drivers']['file']['driver']);
        $this->assertSame(CacheDriver::class, $config['drivers']['cache']['driver']);
    }

    public function testCommandsRegistered(): void
    {
        $this->assertCommandRegistered('up');
        $this->assertCommandRegistered('down');
    }
}
