<?php

declare(strict_types=1);

namespace Spiral\Maintenance\Tests;

use Mockery as m;
use Spiral\Core\FactoryInterface;
use Spiral\Maintenance\Config\MaintenanceConfig;
use Spiral\Maintenance\Driver\DriverInterface;
use Spiral\Maintenance\DriverManager;

final class DriverManagerTest extends TestCase
{
    private m\LegacyMockInterface|m\MockInterface|FactoryInterface $factory;
    private DriverManager $manager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->manager = new DriverManager(
            new MaintenanceConfig([
                'default' => 'foo',
                'drivers' => [
                    'foo' => [
                        'driver' => 'baz',
                        'bar' => 'baf'
                    ],
                ],
            ]),
            $this->factory = m::mock(FactoryInterface::class)
        );
    }

    public function testGetDriver(): void
    {
        $driver = m::mock(DriverInterface::class);

        $this->factory->shouldReceive('make')
            ->once()
            ->with('baz', ['driver' => 'baz', 'bar' => 'baf'])
            ->andReturn($driver);

        $this->assertSame($driver, $this->manager->getDriver());

        // Driver should be init only once
        $this->assertSame($driver, $this->manager->getDriver());
    }
}
