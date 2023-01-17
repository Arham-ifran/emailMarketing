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
        // Commands\WordOfTheDay::class,
        Commands\userFollowUp::class,
        // Commands\CroneJobWorkingNotification::class,
        Commands\GeneratePayAsYouGoPayments::class,
        Commands\MonthlyQuotaRevision::class,
        Commands\SubscriptionExpiredEmailNotifications::class,
        Commands\SubscriptionExpiredEmailNotifications::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('send:crone-email')->everyFiveMinutes();
        $schedule->command('user:inactivity-follow-up')->daily();
        $schedule->command('generate:payments')->weekly();
        $schedule->command('monthly_quota:users')->hourly();
        $schedule->command('package_subscription:expired')->hourly();
        $schedule->command('subscription_expired:notifications')->hourly();
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
