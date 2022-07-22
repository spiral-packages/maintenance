<?php

declare(strict_types=1);

namespace Spiral\Maintenance\Driver;

use Spiral\Files\FilesInterface;
use Spiral\Maintenance\Payload;
use Spiral\Serializer\SerializerInterface;

final class FileDriver implements DriverInterface
{
    private readonly string $path;

    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly FilesInterface $files,
        string $dir,
    ) {
        $this->path = $dir.'/'.'maintenance.json';
    }

    public function activate(Payload $payload): void
    {
        $this->files->write(
            filename: $this->path,
            data: $this->serializer->serialize($payload),
            ensureDirectory: true
        );
    }

    public function deactivate(): void
    {
        $this->files->delete($this->path);
    }

    public function getPayload(): ?Payload
    {
        if (! $this->files->exists($this->path)) {
            return null;
        }

        return $this->serializer->unserialize(
            $this->files->read($this->path),
            Payload::class
        );
    }
}
