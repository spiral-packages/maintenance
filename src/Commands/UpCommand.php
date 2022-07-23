<?php

declare(strict_types=1);

namespace Spiral\Maintenance\Commands;

use Spiral\Console\Command;
use Spiral\Maintenance\MaintenanceMode;

class UpCommand extends Command
{
    protected const SIGNATURE = 'up';
    protected const DESCRIPTION = 'Bring the application out of maintenance mode';

    public function perform(MaintenanceMode $maintenance): int
    {
        try {
            if (! $maintenance->isActive()) {
                $this->output->warning('Application is already up.');

                return self::FAILURE;
            }

            $maintenance->deactivate();

            $this->output->success('Application is now live.');
        } catch (\Throwable $e) {
            $this->output->error(
                \sprintf(
                    'Failed to disable maintenance mode: [%s].',
                    $e->getMessage(),
                )
            );

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
