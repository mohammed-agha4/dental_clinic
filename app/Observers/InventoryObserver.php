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
            // Only trigger if quantity was decreased below reorder level
            if ($inventory->isDirty('quantity') && 
                $inventory->quantity <= $inventory->reorder_level) {

                // Get all admin users
                $admins = User::whereHas('staff', function($query) {
                    $query->where('role', 'admin');
                })->get();
                
                Log::info('Reorder notification triggered for ' . $inventory->name . '. Found ' . $admins->count() . ' admins.');

                // Send notification to each admin
                foreach ($admins as $admin) {
                    $admin->notify(new ReorderNotification($inventory));
                    Log::info('Notification sent to admin ID: ' . $admin->id);
                }
            }
        } catch (\Exception $e) {
            // Log any errors in sending notifications
            Log::error('Failed to send reorder notification: ' . $e->getMessage());
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