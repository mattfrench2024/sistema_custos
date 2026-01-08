<?php

use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| CONTAS A RECEBER — A CADA HORA
|--------------------------------------------------------------------------
*/
Schedule::command('omie:import-receber sv')
    ->hourlyAt(0)
    ->withoutOverlapping();

Schedule::command('omie:import-receber vs')
    ->hourlyAt(5)
    ->withoutOverlapping();

Schedule::command('omie:import-receber gv')
    ->hourlyAt(10)
    ->withoutOverlapping();

/*
|--------------------------------------------------------------------------
| CONTAS A PAGAR — A CADA HORA
|--------------------------------------------------------------------------
*/
Schedule::command('omie:import-pagar sv')
    ->hourlyAt(20)
    ->withoutOverlapping();

Schedule::command('omie:import-pagar vs')
    ->hourlyAt(30)
    ->withoutOverlapping();

Schedule::command('omie:import-pagar gv')
    ->hourlyAt(40)
    ->withoutOverlapping();
