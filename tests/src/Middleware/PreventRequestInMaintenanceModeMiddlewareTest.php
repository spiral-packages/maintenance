<?php

declare(strict_types=1);

namespace Spiral\Maintenance\Tests\Middleware;

use Mockery as m;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Spiral\Maintenance\Driver\DriverInterface;
use Spiral\Maintenance\Exception\MaintenanceModeHttpException;
use Spiral\Maintenance\MaintenanceMode;
use Spiral\Maintenance\Middleware\PreventRequestInMaintenanceModeMiddleware;
use Spiral\Maintenance\Payload;
use Spiral\Maintenance\Tests\TestCase;

final class PreventRequestInMaintenanceModeMiddlewareTest extends TestCase
{
    private \Mockery\LegacyMockInterface|DriverInterface|\Mockery\MockInterface $driver;
    private PreventRequestInMaintenanceModeMiddleware $middleware;

    protected function setUp(): void
    {
        parent::setUp();

        $this->driver = m::mock(DriverInterface::class);

        $this->middleware = new PreventRequestInMaintenanceModeMiddleware(
            new MaintenanceMode($this->driver)
        );
    }

    public function testNotActiveModeShouldHandleNextMiddleware(): void
    {
        $handler = m::mock(RequestHandlerInterface::class);
        $request = m::mock(ServerRequestInterface::class);

        $handler->shouldReceive('handle')
            ->once()
            ->with($request)
            ->andReturn($response = m::mock(ResponseInterface::class));

        $this->driver->shouldReceive('getPayload')->once()->andReturnNull();

        $this->assertSame($response, $this->middleware->process($request, $handler));
    }

    public function testActiveModeShouldThrowAnException(): void
    {
        $this->expectException(MaintenanceModeHttpException::class);
        $this->expectExceptionCode(503);
        $this->expectErrorMessage('Service Unavailable');

        $handler = m::mock(RequestHandlerInterface::class);
        $request = m::mock(ServerRequestInterface::class);

        $this->driver->shouldReceive('getPayload')->twice()->andReturn($payload = new Payload());

        $this->middleware->process($request, $handler);
    }
}
