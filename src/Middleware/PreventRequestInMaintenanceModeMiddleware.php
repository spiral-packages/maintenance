<?php

declare(strict_types=1);

namespace Spiral\Maintenance\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Spiral\Maintenance\Exception\MaintenanceModeHttpException;
use Spiral\Maintenance\MaintenanceMode;

class PreventRequestInMaintenanceModeMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly MaintenanceMode $maintenanceMode
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (! $this->maintenanceMode->isActive()) {
            return $handler->handle($request);
        }

        $payload = $this->maintenanceMode->getPayload();

        throw new MaintenanceModeHttpException($payload);
    }
}
