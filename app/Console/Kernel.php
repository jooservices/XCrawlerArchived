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
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('jav:onejav-new')->everyFiveMinutes();
        $schedule->command('jav:onejav-daily')->dailyAt('12:00');
        $schedule->command('jav:r18-release')->everyTenMinutes();

        /**
         * We have around 10 sub pages (~ 10 fetches) with 30 idols / page,
         * mean this command will generate 300 idols links once executed.
         * And we do process 10 idol / command every 5 minutes,
         * it take us 10 x 12 = 120 idols / hourly
         */
        $schedule->command('jav:xcity-idols')->hourly();
        $schedule->command('jav:xcity-idol')->everyFiveMinutes();

        $schedule->command('jav:xcity-videos')->everyFifteenMinutes();
        $schedule->command('jav:xcity-video')->everyFiveMinutes();
        // $schedule->command('inspire')->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
