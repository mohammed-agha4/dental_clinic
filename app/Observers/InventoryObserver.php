<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Inventory;
use App\Notifications\ReorderNotification;
use Illuminate\Support\Facades\Log;

class InventoryObserver
{
    /**
     * Handle the Inventory "updated" event.
     */
    public function updated(Inventory $inventory): void
    {
        try {
                // isDerty() returns true if the quantity was modified but not yet saved (as here in the)
            if ($inventory->isDirty('quantity') && $inventory->quantity <= $inventory->reorder_level) {
                //whereHas/has ensures the User model has at least one associated staff record that meets the condition
                $admins = User::whereHas('staff', function($query) {
                    $query->where('role', 'admin');
                })->get();

                foreach ($admins as $admin) {
                    $admin->notify(new ReorderNotification($inventory));
                    Log::info('Notification sent');
                }
            }
        } catch (\Exception $e) {
            Log::error('failed to send notification: ' . $e->getMessage());
        }
    }

    /**
     * Handle the Inventory "created" event.
     */
    public function created(Inventory $inventory): void
    {
        // Implementation if needed
    }

    /**
     * Handle the Inventory "deleted" event.
     */
    public function deleted(Inventory $inventory): void
    {
        // Implementation if needed
    }

    /**
     * Handle the Inventory "restored" event.
     */
    public function restored(Inventory $inventory): void
    {
        // Implementation if needed
    }

    /**
     * Handle the Inventory "force deleted" event.
     */
    public function forceDeleted(Inventory $inventory): void
    {
        // Implementation if needed
    }
}
