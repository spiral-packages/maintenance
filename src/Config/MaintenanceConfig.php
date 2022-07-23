<?php

declare(strict_types=1);

namespace Spiral\Maintenance\Config;

use InvalidArgumentException;
use Spiral\Core\InjectableConfig;

final class MaintenanceConfig extends InjectableConfig
{
    public const CONFIG = 'maintenance';
    protected array $config = [
        'default' => '',
        'drivers' => [],
    ];

    public function getDefaultDriver()
    {
        return $this->config['default']
            ?? throw new InvalidArgumentException('Default driver for maintenance config is not specified.');
    }

    public function getDriverConfig(string $driver): array
    {
        if (! isset($this->config['drivers'][$driver])) {
            throw new InvalidArgumentException(
                \sprintf('Config for driver `%s` is not defined.', $driver)
            );
        }

        $config = $this->config['drivers'][$driver];

        if (! isset($config['driver'])) {
            throw new InvalidArgumentException(
                \sprintf('Driver type for `%s` is not defined.', $driver)
            );
        }

        if (! \is_string($config['driver'])) {
            throw new InvalidArgumentException(
                \sprintf('Driver type value for `%s` must be a string.', $driver)
            );
        }

        return $config;
    }
}
