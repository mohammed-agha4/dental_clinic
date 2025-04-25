<?php

namespace App\Models;

use App\Models\Staff;
use App\Models\Visit;
use App\Models\Patient;
use App\Models\Service;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Appointment extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $dates = ['appointment_date'];

    protected $casts = [
        'appointment_date' => 'datetime',
    ];

    // Status constants
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_RESCHEDULED = 'rescheduled';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELED = 'canceled';
    const STATUS_WALK_IN = 'walk_in';

    public function patient()
    {
        return $this->belongsTo(Patient::class)->withDefault();
    }

    public function service()
    {
        return $this->belongsTo(Service::class)->withDefault();
    }

    public function dentist()
    {
        return $this->belongsTo(Staff::class, 'staff_id')->withDefault();
    }

    public function visits()
    {
        return $this->hasMany(Visit::class);
    }

    /**
     * Scope for active appointments (not canceled)
     */
    public function scopeActive($query)
    {
        return $query->where('status', '!=', self::STATUS_CANCELED);
    }



    public function scopeForDentist($query, $userId)
{
    return $query->whereHas('dentist', function($q) use ($userId) {
        $q->where('user_id', $userId);
    });
}



    /**
     * Scope for appointments on a specific date
     */
    public function scopeOnDate($query, $date)
    {
        return $query->whereDate('appointment_date', Carbon::parse($date));
    }

    /**
     * Create a new appointment
     */
    public static function createAppointment(array $data, bool $isWalkIn = false): self
    {
        $appointment = new self();
        $appointment->patient_id = $data['patient_id'];
        $appointment->staff_id = $data['staff_id'];
        $appointment->service_id = $data['service_id'];
        $appointment->duration = Service::findOrFail($data['service_id'])->duration;
        $appointment->notes = $data['notes'] ?? null;
        $appointment->cancellation_reason = $data['cancellation_reason'] ?? null;
        $appointment->reminder_sent = false;

        if ($isWalkIn) {
            $appointment->appointment_date = now();
            $appointment->status = self::STATUS_WALK_IN;
        } else {
            $appointment->appointment_date = $data['appointment_date'];
            $appointment->status = self::STATUS_SCHEDULED;
        }

        $appointment->save();

        return $appointment;
    }

    /**
     * Update appointment details
     */
    public function updateAppointment(array $data): self
    {
        $this->staff_id = $data['staff_id'];
        $this->service_id = $data['service_id'];
        $this->duration = Service::findOrFail($data['service_id'])->duration;
        $this->notes = $data['notes'] ?? $this->notes;
        $this->cancellation_reason = $data['cancellation_reason'] ?? $this->cancellation_reason;

        if (in_array($data['status'], [self::STATUS_SCHEDULED, self::STATUS_RESCHEDULED])) {
            $this->appointment_date = $data['appointment_date'];

            if ($this->getOriginal('status') !== self::STATUS_RESCHEDULED && $data['status'] === self::STATUS_RESCHEDULED) {
                $this->status = self::STATUS_RESCHEDULED;
            } else {
                $this->status = $data['status'];
            }
        } elseif ($data['status'] !== $this->getOriginal('status')) {
            $this->status = $data['status'];
        }

        $this->save();

        return $this;
    }

    /**
     * Check if appointment can be deleted
     */
    public function canBeDeleted(): bool
    {
        return $this->visits()->count() === 0;
    }

    /**
     * Get available time slots for a service on a specific date
     */
    public static function getAvailableSlots(int $serviceId, string $date, int $duration, ?int $currentAppointmentId = null): array
    {
        try {
            $dateObj = Carbon::parse($date);
            $openingTime = Carbon::parse($date . ' 09:00:00');
            $closingTime = Carbon::parse($date . ' 17:00:00');

            $now = Carbon::now();
            if ($dateObj->isToday() && $now->greaterThan($openingTime)) {
                $openingTime = $now->copy()->addHours(1)->startOfHour();
            }

            $staffIds = Staff::where('is_active', true)
                ->whereHas('services', fn($query) => $query->where('service_id', $serviceId))
                ->pluck('id');

            if ($staffIds->isEmpty()) {
                throw new \Exception('No staff available for this service');
            }

            $appointmentsQuery = self::onDate($dateObj)
                ->active()
                ->whereIn('staff_id', $staffIds);

            if ($currentAppointmentId) {
                $appointmentsQuery->where('id', '!=', $currentAppointmentId);
            }

            $existingAppointments = $appointmentsQuery->with('service')->get();

            $slots = [];
            $currentTime = $openingTime->copy();

            while ($currentTime->lessThan($closingTime)) {
                $slotEndTime = $currentTime->copy()->addMinutes($duration);

                if ($slotEndTime->greaterThan($closingTime)) {
                    break;
                }

                $timeString = $currentTime->format('H:i');
                $available = true;

                foreach ($existingAppointments as $appointment) {
                    $appointmentStart = Carbon::parse($appointment->appointment_date);
                    $appointmentDuration = $appointment->service ? $appointment->service->duration : $duration;
                    $appointmentEnd = $appointmentStart->copy()->addMinutes($appointmentDuration);

                    if ($appointmentStart->lt($slotEndTime) && $appointmentEnd->gt($currentTime)) {
                        $available = false;
                        break;
                    }
                }

                $slots[] = [
                    'time' => $timeString,
                    'available' => $available
                ];

                $currentTime->addMinutes($duration);
            }

            return $slots;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get available dentists for a service and time
     */
    public static function getAvailableDentists(int $serviceId, ?string $appointmentDate = null, ?int $currentAppointmentId = null, bool $isWalkIn = false)
    {
        $dentists = Staff::with(['user'])
            ->where('role', 'dentist')
            ->where('is_active', true)
            ->whereHas('services', fn($query) => $query->where('service_id', $serviceId))
            ->get();

        if (!$isWalkIn && $appointmentDate) {
            $appointmentStart = Carbon::parse($appointmentDate);
            $service = Service::findOrFail($serviceId);
            $appointmentEnd = $appointmentStart->copy()->addMinutes($service->duration);

            $dentists = $dentists->filter(function ($dentist) use ($appointmentStart, $appointmentEnd, $currentAppointmentId) {
                $conflicts = self::where('staff_id', $dentist->id)
                    ->active()
                    ->where('id', '!=', $currentAppointmentId)
                    ->where(function ($query) use ($appointmentStart, $appointmentEnd) {
                        $query->where(function ($q) use ($appointmentStart, $appointmentEnd) {
                            $q->where('appointment_date', '>=', $appointmentStart)
                                ->where('appointment_date', '<', $appointmentEnd);
                        })->orWhere(function ($q) use ($appointmentStart, $appointmentEnd) {
                            $q->whereRaw("DATE_ADD(appointment_date, INTERVAL duration MINUTE) > ?", [$appointmentStart])
                                ->whereRaw("appointment_date < ?", [$appointmentEnd]);
                        });
                    })
                    ->count();
                return $conflicts === 0;
            })->values();
        }

        return $dentists;
    }
}
