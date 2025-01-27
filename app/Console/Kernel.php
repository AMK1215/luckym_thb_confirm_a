<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\App;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //Commands\PullReport::class,
        //Commands\ArchiveOldResult::class,
        Commands\DeleteOldWagerBackups::class,
        Commands\ArchiveOldBetNResult::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
          // Run the 'archive:old-result' command daily at midnight 12:00 AM
        $schedule->command('archive:old-result')->dailyAt('00:00');

        //schedules the archive:old-bet-n-result command to run daily at 1:00 AM.
        $schedule->command('archive:old-bet-n-result')->dailyAt('01:00');
        //$schedule->command('make:pull-report')->everyFiveSeconds();
        //$schedule->command('archive:old-result')->everyThirtyMinutes();
        //$schedule->command('archive:old-bet-n-result')->everyThirtyMinutes();
        //$schedule->command('wagers:delete-old-backups')->cron('*/45 * * * *');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}