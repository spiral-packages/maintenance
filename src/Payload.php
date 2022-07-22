<?php

declare(strict_types=1);

namespace Spiral\Maintenance;

final class Payload
{
    public function __construct(
        public readonly ?\DateTimeInterface $retryAt = null,
        public readonly int $responseStatus = 503
    ) {
    }
}
