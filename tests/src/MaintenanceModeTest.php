<?php

declare(strict_types=1);

namespace Spiral\Maintenance\Tests;

use Mockery as m;
use Spiral\Maintenance\Driver\DriverInterface;
use Spiral\Maintenance\MaintenanceMode;
use Spiral\Maintenance\Payload;

final class MaintenanceModeTest extends TestCase
{
    private MaintenanceMode $mode;
    private m\LegacyMockInterface|DriverInterface|m\MockInterface $driver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mode = new MaintenanceMode(
            $this->driver = m::mock(DriverInterface::class)
        );
    }

    public function testActivate(): void
    {
        $this->driver
            ->shouldReceive('activate')
            ->once()
            ->withArgs(function (Payload $payload) {
                return $payload->responseStatus === 503
                    && $payload->retryAt === null;
            });

        $this->mode->activate();
    }

    public function testActivateWithResponseStatus(): void
    {
        $this->driver
            ->shouldReceive('activate')
            ->once()
            ->withArgs(function (Payload $payload) {
                return $payload->responseStatus === 504
                    && $payload->retryAt === null;
            });

        $this->mode->setResponseStatus(504)->activate();
    }

    public function testActivateWithRetryAt(): void
    {
        $date = new \DateTimeImmutable();

        $this->driver
            ->shouldReceive('activate')
            ->once()
            ->withArgs(function (Payload $payload) use($date) {
                return $payload->responseStatus === 503
                    && $payload->retryAt === $date;
            });

        $this->mode->setRetryAt($date)->activate();
    }

    public function testDeactivate(): void
    {
        $this->driver
            ->shouldReceive('deactivate')
            ->once();

        $this->mode->deactivate();
    }

    public function testGetPayload(): void
    {
        $this->driver
            ->shouldReceive('getPayload')
            ->once()
            ->andReturn($payload = new Payload());

        $this->assertSame($payload, $this->mode->getPayload());
    }

    public function testIsActive(): void
    {
        $this->driver
            ->shouldReceive('getPayload')
            ->once()
            ->andReturn(new Payload());

        $this->assertTrue($this->mode->isActive());

        $this->driver
            ->shouldReceive('getPayload')
            ->once()
            ->andReturnNull();

        $this->assertFalse($this->mode->isActive());
    }
}
