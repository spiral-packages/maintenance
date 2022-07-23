<?php

declare(strict_types=1);

namespace Spiral\Maintenance\Tests\Config;

use InvalidArgumentException;
use Spiral\Maintenance\Config\MaintenanceConfig;
use Spiral\Maintenance\Tests\TestCase;

final class MaintenanceConfigTest extends TestCase
{
    public function testGetsDefaultDriver(): void
    {
        $config = new MaintenanceConfig([
            'default' => 'foo'
        ]);

        $this->assertSame('foo', $config->getDefaultDriver());
    }

    public function testNotSpecifiedDefaultDriver(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectErrorMessage('Default driver for maintenance config is not specified.');

        $config = new MaintenanceConfig();
        $config->getDefaultDriver();
    }

    public function testGetsDriverConfig(): void
    {
        $config = new MaintenanceConfig([
            'drivers' => [
                'foo' => [
                    'driver' => 'bar',
                    'baz' => 'baf'
                ]
            ]
        ]);

        $this->assertSame([
            'driver' => 'bar',
            'baz' => 'baf'
        ], $config->getDriverConfig('foo'));
    }

    public function testGetsNonExistsDriver(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectErrorMessage('Config for driver `foo` is not defined.');
        $config = new MaintenanceConfig([
            'drivers' => []
        ]);

        $config->getDriverConfig('foo');
    }

    public function testGetsDriverWithoutConfiguration(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectErrorMessage('Driver type for `foo` is not defined.');
        $config = new MaintenanceConfig([
            'drivers' => [
                'foo' => []
            ]
        ]);

        $config->getDriverConfig('foo');
    }

    public function testGetsInvalidDriver(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectErrorMessage('Driver type value for `foo` must be a string.');
        $config = new MaintenanceConfig([
            'drivers' => [
                'foo' => [
                    'driver' => [],
                ]
            ]
        ]);

        $config->getDriverConfig('foo');
    }
}
