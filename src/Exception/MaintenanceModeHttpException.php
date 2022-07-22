<?php

declare(strict_types=1);

namespace Spiral\Maintenance\Exception;

use Spiral\Http\Exception\ClientException;
use Spiral\Maintenance\Payload;

class MaintenanceModeHttpException extends ClientException
{
    public function __construct(
        public readonly Payload $payload
    ) {
        parent::__construct($payload->responseStatus, 'Service Unavailable');
    }
}
