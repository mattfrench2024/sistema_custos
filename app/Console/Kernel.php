<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
   protected function schedule(Schedule $schedule): void
{
    /*
     |--------------------------------------------------------------------------
     | CONTAS A RECEBER (já existente)
     |--------------------------------------------------------------------------
     */
    $schedule->command('omie:import-receber sv')
        ->dailyAt('01:00')
        ->withoutOverlapping();

    $schedule->command('omie:import-receber vs')
        ->dailyAt('01:05')
        ->withoutOverlapping();

    $schedule->command('omie:import-receber gv')
        ->dailyAt('01:10')
        ->withoutOverlapping();

    /*
     |--------------------------------------------------------------------------
     | CONTAS A PAGAR — A CADA HORA
     |--------------------------------------------------------------------------
     */
    $schedule->command('omie:import-pagar sv')
        ->hourlyAt(20)
        ->withoutOverlapping();

    $schedule->command('omie:import-pagar vs')
        ->hourlyAt(30)
        ->withoutOverlapping();

    $schedule->command('omie:import-pagar gv')
        ->hourlyAt(40)
        ->withoutOverlapping();
}

    

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
