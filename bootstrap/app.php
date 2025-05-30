<?php
use Illuminate\Foundation\Application;
use Illuminate\Console\Scheduling\Schedule;
use App\Console\Commands\SendAppointmentReminders;
use App\Console\Commands\CheckInventoryExpiration;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\EagerLoadUserRelations::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->withCommands([
        SendAppointmentReminders::class,
        CheckInventoryExpiration::class,
    ])
    ->withSchedule(function (Schedule $schedule) {
        $schedule->command('appointments:send-reminders')
            ->dailyAt('09:00')
            ->timezone('Asia/Gaza')
            ->onOneServer();


        $schedule->command('inventory:check-expiration')
            ->dailyAt('10:00')
            ->timezone('Asia/Gaza')
            ->onOneServer();
    })->create();
