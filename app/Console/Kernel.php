<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Jadwal cron aplikasi (tidak ada task terjadwal saat ini)
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        require base_path('routes/console.php');
    }
}
