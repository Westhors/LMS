<?php

namespace App\Console\Commands;

use App\Models\Teacher;
use Illuminate\Console\Command;

class UpdateTeachersStatus extends Command
{
    protected $signature = 'teachers:update-status';

    protected $description = 'Update teachers active status based on expire_date';

    public function handle(): int
    {
        Teacher::whereNotNull('expire_date')
            ->whereDate('expire_date', '<', today())
            ->update([
                'active' => 0,
            ]);

        Teacher::whereNotNull('expire_date')
            ->whereDate('expire_date', '>=', today())
            ->update([
                'active' => 1,
            ]);

        $this->info('Teachers status updated successfully.');

        return self::SUCCESS;
    }
}
