<?php

declare(strict_types=1);

namespace Spiral\Maintenance\Bootloader;

use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Boot\DirectoriesInterface;
use Spiral\Boot\EnvironmentInterface;
use Spiral\Config\ConfiguratorInterface;
use Spiral\Maintenance\Commands;
use Spiral\Maintenance\Config\MaintenanceConfig;
use Spiral\Console\Bootloader\ConsoleBootloader;
use Spiral\Maintenance\Driver\CacheDriver;
use Spiral\Maintenance\Driver\DriverInterface;
use Spiral\Maintenance\Driver\FileDriver;
use Spiral\Maintenance\DriverManager;

class MaintenanceBootloader extends Bootloader
{
    protected const DEPENDENCIES = [
        ConsoleBootloader::class,
    ];

    protected const SINGLETONS = [
        DriverManager::class => DriverManager::class,
        DriverInterface::class => [self::class, 'initDriver'],
    ];

    public function __construct(
        private readonly ConfiguratorInterface $config
    ) {
    }

    public function init(
        EnvironmentInterface $env,
        DirectoriesInterface $dirs,
        ConsoleBootloader $console
    ): void {
        $this->initConfig($env, $dirs);

        $console->addCommand(Commands\UpCommand::class);
        $console->addCommand(Commands\DownCommand::class);
    }

    private function initDriver(DriverManager $manager): DriverInterface
    {
        return $manager->getDriver();
    }

    private function initConfig(
        EnvironmentInterface $env,
        DirectoriesInterface $dirs
    ): void {
        $this->config->setDefaults(
            MaintenanceConfig::CONFIG,
            [
                'default' => $env->get('MAINTENANCE_DRIVER', 'file'),
                'drivers' => [
                    'file' => [
                        'driver' => FileDriver::class,
                        'dir' => $dirs->get('runtime'),
                    ],
                    'cache' => [
                        'driver' => CacheDriver::class,
                        'storage' => $env->get('MAINTENANCE_CACHE_STORAGE'),
                        'key' => $env->get('MAINTENANCE_CACHE_KEY', 'maintenance'),
                    ],
                ],
            ]
        );
    }
}
