<?php

declare(strict_types=1);

namespace Spiral\Maintenance;

final class Payload implements \JsonSerializable
{
    public function __construct(
        public readonly \DateTimeInterface $retryAt,
        public readonly int $responseStatus = 503
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'retryAt' => $this->retryAt->getTimestamp(),
            'responseStatus' => $this->responseStatus,
        ];
    }
}
