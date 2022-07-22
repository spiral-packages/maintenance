<?php

declare(strict_types=1);

namespace Spiral\Maintenance;

use Spiral\Maintenance\Driver\DriverInterface;

final class MaintenanceMode
{
    private \DateTimeInterface $retryAt;
    private int $responseStatus = 503;

    public function __construct(
        private readonly DriverInterface $driver
    ) {
    }

    public function setResponseStatus(int $responseStatus): self
    {
        $this->responseStatus = $responseStatus;

        return $this;
    }

    public function setRetryAt(\DateTimeImmutable $retryAt): self
    {
        $this->retryAt = $retryAt;

        return $this;
    }

    public function activate(): void
    {
        $this->driver->activate($this->makePayload());
    }

    public function deactivate(): void
    {
        $this->driver->deactivate();
    }

    public function getPayload(): ?Payload
    {
        return $this->driver->getPayload();
    }

    public function isActive(): bool
    {
        return $this->getPayload() !== null;
    }

    private function makePayload(): Payload
    {
        return new Payload(
            $this->retryAt,
            $this->responseStatus
        );
    }
}
