<?php

namespace App\Models;

use App\Models\Staff;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Service;
use App\Models\Inventory;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Models\InventoryVisit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Visit extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    protected $casts = [
        'visit_date' => 'datetime',  // This ensures it's always a Carbon instance
        'appointment_date' => 'datetime',
    ];
    // Status constants
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_WALK_IN = 'walk_in';
    const STATUS_COMPLETED = 'completed';
    const STATUS_RESCHEDULED = 'rescheduled';
    const STATUS_CANCELED = 'canceled';

    // Transaction types
    const TRANSACTION_PURCHASE = 'purchase';
    const TRANSACTION_USE = 'use';
    const TRANSACTION_ADJUSTMENT = 'adjustment';
    const TRANSACTION_RETURN = 'return';

    public function appointment()
    {
        return $this->belongsTo(Appointment::class)->withDefault();
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class)->withDefault();
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class)->withDefault();
    }

    public function service()
    {
        return $this->belongsTo(Service::class)->withDefault();
    }

    public function inventoryItems()
    {
        return $this->belongsToMany(Inventory::class, 'inventory_visits')
            ->withPivot('quantity_used', 'notes')
            ->withTimestamps();
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Create a new visit from request data
     */
    public static function createVisit(array $data): self
    {
        $visit = new self();
        $visit->appointment_id = $data['appointment_id'];
        $visit->patient_id = $data['patient_id'];
        $visit->staff_id = $data['staff_id'];
        $visit->service_id = $data['service_id'];
        $visit->visit_date = $data['visit_date'];
        $visit->cheif_complaint = $data['cheif_complaint'] ?? null;
        $visit->diagnosis = $data['diagnosis'] ?? null;
        $visit->treatment_notes = $data['treatment_notes'] ?? null;
        $visit->next_visit_notes = $data['next_visit_notes'] ?? null;
        $visit->save();

        return $visit;
    }

    /**
     * Update visit details
     */
    public function updateVisit(array $data): self
    {
        $this->appointment_id = $data['appointment_id'];
        $this->patient_id = $data['patient_id'];
        $this->staff_id = $data['staff_id'];
        $this->service_id = $data['service_id'];
        $this->visit_date = $data['visit_date'];
        $this->cheif_complaint = $data['cheif_complaint'] ?? $this->cheif_complaint;
        $this->diagnosis = $data['diagnosis'] ?? $this->diagnosis;
        $this->treatment_notes = $data['treatment_notes'] ?? $this->treatment_notes;
        $this->next_visit_notes = $data['next_visit_notes'] ?? $this->next_visit_notes;
        $this->save();

        return $this;

    }

    /**
     * Process inventory transactions for this visit
     */
    public function processInventoryTransactions(array $transactions): void
    {
        foreach ($transactions['transaction_inventory_ids'] as $index => $inventoryId) {

            if (empty($inventoryId)) continue;

            $type = $transactions['transaction_types'][$index] ?? self::TRANSACTION_USE;
            $quantity = $transactions['transaction_quantities'][$index] ?? 1;
            $unitPrice = $transactions['transaction_prices'][$index] ?? 0;
            $date = $transactions['transaction_dates'][$index] ?? now()->format('Y-m-d');
            $note = $transactions['transaction_notes'][$index] ?? null;

            $inventoryItem = Inventory::findOrFail($inventoryId);

            if ($inventoryItem->is_expired) {
                throw new \Exception("Cannot use expired item: {$inventoryItem->name}");
            }

            // Create inventory transaction
            $transaction = InventoryTransaction::create([
                'inventory_id' => $inventoryId,
                'staff_id' => $this->staff_id,
                'type' => $type,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'transaction_date' => $date,
                'notes' => $note ?: "Transaction during visit #{$this->id}"
            ]);

            // Handle inventory quantity updates
            $this->updateInventoryQuantity($inventoryItem, $type, $quantity);

            // Create inventory_visit relation for items that are "used"
            if ($type === self::TRANSACTION_USE) {
                InventoryVisit::create([
                    'visit_id' => $this->id,
                    'inventory_id' => $inventoryId,
                    'quantity_used' => $quantity,
                    'notes' => $note
                ]);
            }
        }
    }

    /**
     * Update inventory quantity based on transaction type
     */
    protected function updateInventoryQuantity(Inventory $inventory, string $type, int $quantity): void
    {
        switch ($type) {
            case self::TRANSACTION_PURCHASE:
                $inventory->increment('quantity', $quantity);
                break;

            case self::TRANSACTION_USE:
            case self::TRANSACTION_RETURN:
                if ($inventory->quantity < $quantity) {
                    throw new \Exception("Not enough inventory for item: {$inventory->name}");
                }
                $inventory->decrement('quantity', $quantity);
                break;

            case self::TRANSACTION_ADJUSTMENT:
                $inventory->update(['quantity' => $quantity]);
                break;
        }
    }

    /**
     * Check if visit can be deleted
     */
    public function canBeDeleted(): bool
    {
        return !$this->payments()->exists();
    }

    /**
     * Prepare inventory transactions data from request
     */
    public static function prepareInventoryData(Request $request): array
    {
        return [
            'transaction_inventory_ids' => $request->transaction_inventory_ids ?? [],
            'transaction_types' => $request->transaction_types ?? [],
            'transaction_quantities' => $request->transaction_quantities ?? [],
            'transaction_prices' => $request->transaction_prices ?? [],
            'transaction_dates' => $request->transaction_dates ?? [],
            'transaction_notes' => $request->transaction_notes ?? [],
        ];
    }


    public function scopeForDentist($query, $userId)
    {
        return $query->whereHas('staff', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }

    /**
     * Restore inventory quantities for items used in this visit
     */
    public function restoreInventoryQuantities(): void
    {
        // Get all inventory items used in this visit
        $inventoryVisits = InventoryVisit::where('visit_id', $this->id)->get();

        foreach ($inventoryVisits as $inventoryVisit) {
            $inventoryItem = Inventory::find($inventoryVisit->inventory_id);

            if ($inventoryItem) {
                // Restore the quantity that was used
                $inventoryItem->increment('quantity', $inventoryVisit->quantity_used);

                // Create a transaction record for the restoration
                InventoryTransaction::create([
                    'inventory_id' => $inventoryItem->id,
                    'staff_id' => $this->staff_id,
                    'type' => self::TRANSACTION_RETURN,
                    'quantity' => $inventoryVisit->quantity_used,
                    'unit_price' => $inventoryItem->unit_price,
                    'transaction_date' => now(),
                    'notes' => "Quantity restored from deleted visit #{$this->id}"
                ]);
            }
        }
    }
}
