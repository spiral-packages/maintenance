<?php

declare(strict_types=1);

namespace Spiral\Maintenance\Commands;

use Spiral\Console\Command;
use Spiral\Maintenance\MaintenanceMode;

class DownCommand extends Command
{
    protected const SIGNATURE = 'maintenance:down 
                {--retry= : The number of seconds after which the request may be retried}
                {--status=503 : The status code that should be used when returning the maintenance mode response}';

    protected const DESCRIPTION = 'Put the application into maintenance mode';

    public function perform(MaintenanceMode $maintenance): int
    {
        try {
            if ($maintenance->isActive()) {
                $this->output->warning('Application is already down.');

                return self::FAILURE;
            }

            if ($this->hasOption('retry')) {
                $date = new \DateTimeImmutable();
                $date->add(new \DateInterval(\sprintf('PT%dS', (int)$this->option('retry'))));
                $maintenance->setRetryAt($date);
            }

            if ($this->hasOption('status')) {
                $maintenance->setResponseStatus((int)($this->option('status') ?? 503));
            }

            $maintenance
                ->activate();

            $this->output->success('Application is now in maintenance mode.');
        } catch (\Throwable $e) {
            $this->output->error(
                \sprintf(
                    'Failed to enter maintenance mode: %s.',
                    $e->getMessage(),
                )
            );

            return self::FAILURE;
        }
    }
}
