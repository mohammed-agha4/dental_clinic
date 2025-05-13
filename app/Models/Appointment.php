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
        return $query->whereHas('dentist', function ($q) use ($userId) {
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
    public static function createAppointment(array $data, bool $isWalkIn = false): self //array $data: holds the passed array from controller
    {
        $appointment = new self();
        $appointment->patient_id = $data['patient_id'];
        $appointment->staff_id = $data['staff_id'];
        $appointment->service_id = $data['service_id'];
        $appointment->duration = Service::findOrFail($data['service_id'])->duration;
        $appointment->notes = $data['notes'] ?? null;
        // $appointment->cancellation_reason = $data['cancellation_reason'] ?? null;
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
        // $this->cancellation_reason = $data['cancellation_reason'] ?? $this->cancellation_reason;

        if (in_array($data['status'], [self::STATUS_SCHEDULED, self::STATUS_RESCHEDULED])) { //if the request is scheduled or rescheduled, the condition returns true

            $this->appointment_date = $data['appointment_date'];


            // $this->getOriginal('status'): This retrieves the original status of the appointment before any changes.
            // !== self::STATUS_RESCHEDULED: This ensures the original status was NOT 'rescheduled'.
            // $data['status'] === self::STATUS_RESCHEDULED: This checks if the new status being set is 'rescheduled'.
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



            if ($dateObj->isToday() && $now->greaterThan($openingTime)) { // This checks if the given $date is today & current time has already passed 09:00 AM

                // - Since the original opening time (09:00 AM) is already in the past, the function adjusts it. The new opening time becomes one hour ahead from now, rounded to the next full hour.
                $openingTime = $now->copy()->addHours(1)->startOfHour();
            }




            $staffIds = Staff::where('is_active', true)
                ->whereHas('services', fn($query) => $query->where('service_id', $serviceId)) //- Filters the staff further, ensuring they are linked to the requested service ($serviceId)

                ->pluck('id'); // to retieve only the id (not the full staff record)

            if ($staffIds->isEmpty()) {
                throw new \Exception('No staff available for this service');
            }

            $appointmentsQuery = self::onDate($dateObj) //self: php way to call another function in the same class ,so its calling scopeOnDate()
                ->active()
                ->whereIn('staff_id', $staffIds);

            if ($currentAppointmentId) {
                $appointmentsQuery->where('id', '!=', $currentAppointmentId);
            }

            $existingAppointments = $appointmentsQuery->with('service')->get();

            $slots = [];
            $currentTime = $openingTime->copy(); //- copy() creates a separate instance of openingTime, storing it in $currentTime.This prevents accidental changes to openingTime when $currentTime is modified later in the loop.


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
                $conflicts = self::where('staff_id', $dentist->id) //Searches the Appointment model for existing appointments assigned to this dentist.

                    ->active()  //scope
                    ->where('id', '!=', $currentAppointmentId) //- Ensures that the current appointment (if being edited) is not counted as a conflict

                    ->where(function ($query) use ($appointmentStart, $appointmentEnd) {
                        $query->where(function ($q) use ($appointmentStart, $appointmentEnd) {
                            $q->where('appointment_date', '>=', $appointmentStart)
                                ->where('appointment_date', '<', $appointmentEnd);
                        })->orWhere(function ($q) use ($appointmentStart, $appointmentEnd) {
                            $q->whereRaw("DATE_ADD(appointment_date, INTERVAL duration MINUTE) > ?", [$appointmentStart])
                            /* Start Time: 10:00 AM,  Duration: 30 minutes,  End Time (calculated by DATE_ADD): 10:30 AM
                            Now, assume we're checking availability for: Requested Start Time: 10:15 AM
                            The condition: DATE_ADD('10:00 AM', INTERVAL 30 MINUTE) > '10:15 AM'
                            becomes: '10:30 AM' > '10:15 AM' (Overlap detected)
                            Since 10:30 AM is greater than 10:15 AM, it means this appointment overlaps, and the dentist is not available.*/



                                ->whereRaw("appointment_date < ?", [$appointmentEnd]);
                        });
                    })->count();

                return $conflicts === 0;
            })->values(); // holding the filter values
        }

        return $dentists;
    }
}
