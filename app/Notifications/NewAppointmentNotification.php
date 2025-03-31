<?php

namespace App\Notifications;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Service;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class NewAppointmentNotification extends Notification
{
    protected $appointment;
    protected $isUpdate;

    public function __construct(Appointment $appointment, bool $isUpdate = false)
    {
        $this->appointment = $appointment;
        $this->isUpdate = $isUpdate;
    }

    public function via(object $notifiable): array
    {
        return ['database']; // Only use the database channel
    }

    public function toDatabase($notifiable)
    {
        // Get related models
        $patient = Patient::find($this->appointment->patient_id);
        $service = Service::find($this->appointment->service_id);

        // Format patient name
        $patientName = $patient ? $patient->fname . ' ' . $patient->lname : 'Unknown Patient';

        // Format appointment time
        $appointmentDate = \Carbon\Carbon::parse($this->appointment->appointment_date)->format('M d, Y h:i A');

        // Create appropriate message based on whether it's a new or updated appointment
        if ($this->isUpdate) {
            $statusText = Str::ucfirst($this->appointment->status);
            $message = "Appointment updated: $patientName - $statusText on $appointmentDate";
        } else {
            $message = "New appointment: $patientName on $appointmentDate";
        }

        return [
            'message' => $message,
            'patient_name' => $patientName,
            'service_name' => $service ? $service->service_name : 'Unknown Service',
            'appointment_date' => $appointmentDate,
            'duration' => $this->appointment->duration,
            'status' => $this->appointment->status,
            'url' => route('notifications.index', $this->appointment->id),
            'icon' => 'fas fa-calendar-check',
            'appointment_id' => $this->appointment->id,
            'is_update' => $this->isUpdate
        ];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'appointment_id' => $this->appointment->id,
            'message' => $this->isUpdate ?
                "Appointment updated for patient ID: {$this->appointment->patient_id}" :
                "New appointment created for patient ID: {$this->appointment->patient_id}",
        ];
    }
}
