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
        // Existing appointment reminders
        $schedule->command('appointments:send-reminders')
            ->dailyAt('09:00')
            ->timezone('Your/Timezone') // e.g. 'America/New_York'
            ->onOneServer(); // If running on multiple servers

        // New inventory expiration check
        $schedule->command('inventory:check-expiration')
            ->dailyAt('10:00') // Run at 10 AM
            ->timezone('Your/Timezone') // Use the same timezone as your other command
            ->onOneServer();
    })->create();
