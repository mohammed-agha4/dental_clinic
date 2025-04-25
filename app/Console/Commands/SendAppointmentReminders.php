<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Appointment;
use App\Notifications\AppointmentReminder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendAppointmentReminders extends Command
{
    protected $signature = 'appointments:send-reminders';
    protected $description = 'Send reminders for appointments scheduled for tomorrow';

    public function handle()
{
    $this->info('Starting appointment reminders...');

    $appointments = Appointment::with(['patient', 'service'])
        ->whereDate('appointment_date', now()->addDay()->format('Y-m-d'))
        ->where('reminder_sent', false)
        ->whereIn('status', ['scheduled', 'rescheduled'])
        ->get();

    if ($appointments->isEmpty()) {
        $this->info('No appointments requiring reminders found.');
        return;
    }

    $this->info("Found {$appointments->count()} appointments needing reminders");

    foreach ($appointments as $appointment) {
        try {
            $this->line("Processing appointment #{$appointment->id} for {$appointment->patient->full_name}");

            if (!$appointment->patient->email) {
                $this->error("No email for patient ID {$appointment->patient->id}");
                continue;
            }

            $appointment->patient->notify(new \App\Notifications\AppointmentReminder($appointment));
            $appointment->update(['reminder_sent' => true]);

            $this->info("Sent to: {$appointment->patient->email}");

        } catch (\Exception $e) {
            $this->error("Failed for appointment #{$appointment->id}: " . $e->getMessage());
            logger()->error("Reminder failed for appointment #{$appointment->id}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    $this->info('Reminder process completed');
}
}
