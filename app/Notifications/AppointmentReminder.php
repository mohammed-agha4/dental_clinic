<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public $appointment;

    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    // REQUIRED METHOD
    public function via($notifiable)
    {
        return ['mail']; // Can also add 'database' if you want to store notifications
    }

    // REQUIRED METHOD
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Appointment Reminder - ' . config('app.name'))
            ->greeting("Hello {$this->appointment->patient->fname},")
            ->line('Your dental appointment is scheduled for:')
            ->line('**Date:** ' . $this->appointment->appointment_date->format('l, F j, Y'))
            ->line('**Time:** ' . $this->appointment->appointment_date->format('g:i A'))
            ->line('Please arrive 10 minutes early.');
    }

    // Optional: For database notifications
    public function toArray($notifiable)
    {
        return [
            'appointment_id' => $this->appointment->id,
            'date' => $this->appointment->appointment_date->toDateTimeString()
        ];
    }
}
