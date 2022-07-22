<?php

declare(strict_types=1);

namespace Spiral\Maintenance;

use Spiral\Core\FactoryInterface;
use Spiral\Maintenance\Config\MaintenanceConfig;
use Spiral\Maintenance\Driver\DriverInterface;
use Spiral\Maintenance\Exception\DriverNotFoundException;

final class DriverManager
{
    /** @var DriverInterface[] */
    private array $drivers = [];

    public function __construct(
        private readonly MaintenanceConfig $config,
        private readonly FactoryInterface $factory
    ) {
    }

    public function getDriver(): DriverInterface
    {
        $driver = $this->config->getDefaultDriver();

        if (! isset($this->drivers[$driver])) {
            throw new DriverNotFoundException(\sprintf('Driver [%s] is not found.', $driver));
        }

        return $this->drivers[$driver] = $this->resolve($driver);
    }

    private function resolve(string $driver): DriverInterface
    {
        $config = $this->config->getDriverConfig($driver);

        return $this->factory->make($config['driver'], $config);
    }
}
