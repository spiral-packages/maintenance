<?php

declare(strict_types=1);

namespace Spiral\Maintenance\Exception;

use Spiral\Http\Exception\ClientException;
use Spiral\Maintenance\Payload;

class MaintenanceModeHttpException extends ClientException
{
    public readonly Payload $payload;

    public function withPayload(Payload $payload): self
    {
        $self = clone $this;
        $self->payload = $payload;

        return $self;
    }
}
