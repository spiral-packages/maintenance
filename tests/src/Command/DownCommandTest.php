<?php

declare(strict_types=1);

namespace Spiral\Maintenance\Tests\Command;

use Spiral\Maintenance\Driver\DriverInterface;
use Spiral\Maintenance\Payload;
use Spiral\Maintenance\Tests\TestCase;

final class DownCommandTest extends TestCase
{
    public function testDownAppInMaintenanceMode(): void
    {
        $driver = $this->mockContainer(DriverInterface::class);

        $driver->shouldReceive('getPayload')->once()->andReturn(new Payload());

        $this->assertConsoleCommandOutputContainsStrings('down', [], [
            'Application is already down.'
        ]);
    }

    public function testDownApp(): void
    {
        $driver = $this->mockContainer(DriverInterface::class);

        $driver->shouldReceive('getPayload')->once()->andReturnNull();
        $driver->shouldReceive('activate')->once()->withArgs(function (Payload $payload) {
            return $payload->responseStatus === 503
                && $payload->retryAt === null;
        });

        $this->assertConsoleCommandOutputContainsStrings('down', [], [
            'Application is now in maintenance mode.'
        ]);
    }

    public function testDownAppWithStatus(): void
    {
        $driver = $this->mockContainer(DriverInterface::class);

        $driver->shouldReceive('getPayload')->once()->andReturnNull();
        $driver->shouldReceive('activate')->once()->withArgs(function (Payload $payload) {
            return $payload->responseStatus === 504
                && $payload->retryAt === null;
        });

        $this->assertConsoleCommandOutputContainsStrings('down', [
            '--status' => 504
        ], [
            'Application is now in maintenance mode.'
        ]);
    }

    public function testDownAppWithRetry(): void
    {
        $driver = $this->mockContainer(DriverInterface::class);

        $date = new \DateTimeImmutable();
        $date->add(new \DateInterval('PT600S'));

        $driver->shouldReceive('getPayload')->once()->andReturnNull();
        $driver->shouldReceive('activate')->once()->withArgs(function (Payload $payload) use($date) {
            return $payload->responseStatus === 503
                && $payload->retryAt->getTimestamp() === $date->getTimestamp();
        });

        $this->assertConsoleCommandOutputContainsStrings('down', [
            '--retry' => 600
        ], [
            'Application is now in maintenance mode.'
        ]);
    }

    public function testUpWitErrors(): void
    {
        $driver = $this->mockContainer(DriverInterface::class);

        $driver->shouldReceive('getPayload')->once()->andThrow(new \Exception('Something went wrong.'));

        $this->assertConsoleCommandOutputContainsStrings('down', [], [
            'Failed to enter maintenance mode: [Something went wrong.].'
        ]);
    }
}
