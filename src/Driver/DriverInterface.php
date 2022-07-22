<?php

declare(strict_types=1);

namespace Spiral\Maintenance\Driver;

use Spiral\Maintenance\Payload;

interface DriverInterface
{
    public function activate(Payload $payload): void;

    public function deactivate(): void;

    public function getPayload(): ?Payload;
}
