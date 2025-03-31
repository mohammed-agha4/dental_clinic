<?php

namespace App\Observers;

use App\Models\Appointment;
use App\Models\Staff;
use App\Models\User;
use App\Notifications\NewAppointmentNotification;
use Illuminate\Support\Facades\Log;

class AppointmentObserver
{
    /**
     * Handle the Appointment "created" event.
     */
    public function created(Appointment $appointment)
    {
        Log::info("Attempting to send notification for appointment {$appointment->id}");

        try {
            $doctor = Staff::with('user')->find($appointment->staff_id);
            
            if (!$doctor) {
                Log::error("No staff member found for appointment");
                return;
            }

            if ($doctor->role !== 'dentist') {
                Log::info("Staff member is not a dentist, skipping notification");
                return;
            }

            if (!$doctor->user) {
                Log::error("No user account linked to dentist");
                return;
            }

            Log::info("Sending notification to user {$doctor->user->id}");
            
            $doctor->user->notify(new NewAppointmentNotification($appointment));
            
            Log::info("Notification sent successfully");
            
        } catch (\Exception $e) {
            Log::error("Failed to send notification: " . $e->getMessage());
            Log::error($e->getTraceAsString());
        }
    }


    /**
     * Handle the Appointment "updated" event.
     */
    public function updated(Appointment $appointment): void
    {
        // Implementation if needed for appointment updates
        // For example, if an appointment is rescheduled
        if ($appointment->isDirty('appointment_date') || $appointment->isDirty('status')) {
            try {
                // Get the doctor (staff) assigned to this appointment
                $doctor = Staff::find($appointment->staff_id);

                if ($doctor && $doctor->role === 'dentist') {
                    // Get the user associated with the doctor
                    $user = User::find($doctor->user_id);

                    if ($user) {
                        Log::info('Appointment update notification triggered for doctor ID: ' . $doctor->id);
                        $user->notify(new NewAppointmentNotification($appointment, true));
                        Log::info('Update notification sent to doctor user ID: ' . $user->id);
                    }
                }
            } catch (\Exception $e) {
                // Log any errors in sending notifications
                Log::error('Failed to send appointment update notification: ' . $e->getMessage());
            }
        }
    }

    /**
     * Handle the Appointment "deleted" event.
     */
    public function deleted(Appointment $appointment): void
    {
        // Implementation if needed
    }

    /**
     * Handle the Appointment "restored" event.
     */
    public function restored(Appointment $appointment): void
    {
        // Implementation if needed
    }

    /**
     * Handle the Appointment "force deleted" event.
     */
    public function forceDeleted(Appointment $appointment): void
    {
        // Implementation if needed
    }
}
