<?php

declare(strict_types=1);

namespace Spiral\Maintenance\Tests\Command;

use Spiral\Maintenance\Driver\DriverInterface;
use Spiral\Maintenance\Payload;
use Spiral\Maintenance\Tests\TestCase;

final class UpCommandTest extends TestCase
{
    public function testDownAppInMaintenanceMode(): void
    {
        $driver = $this->mockContainer(DriverInterface::class);

        $driver->shouldReceive('getPayload')->once()->andReturn(new Payload());
        $driver->shouldReceive('deactivate')->once();

        $this->assertConsoleCommandOutputContainsStrings('up', [], [
            'Application is now live.'
        ]);
    }

    public function testUpWorkingApp(): void
    {
        $driver = $this->mockContainer(DriverInterface::class);

        $driver->shouldReceive('getPayload')->once()->andReturnNull();

        $this->assertConsoleCommandOutputContainsStrings('up', [], [
            'Application is already up.'
        ]);
    }

    public function testUpWitErrors(): void
    {
        $driver = $this->mockContainer(DriverInterface::class);

        $driver->shouldReceive('getPayload')->once()->andThrow(new \Exception('Something went wrong.'));

        $this->assertConsoleCommandOutputContainsStrings('up', [], [
            'Failed to disable maintenance mode: [Something went wrong.].'
        ]);
    }
}
