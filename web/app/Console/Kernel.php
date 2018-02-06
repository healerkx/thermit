<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
        'App\Console\Commands\ActionCommand',
        'App\Console\Commands\ControllerCommand',
        'App\Console\Commands\DocCommand',
        'App\Console\Commands\ModelCommand',
        'App\Console\Commands\AdminAddCURDCommand',
        'App\Console\Commands\AdminAddBlankCommand',
        'App\Console\Commands\RouteCommand',
        'App\Console\Commands\RestfulCommand',
        'App\Console\Commands\TableCommand'
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
