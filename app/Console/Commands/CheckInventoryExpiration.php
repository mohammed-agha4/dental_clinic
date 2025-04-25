<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Inventory;
use App\Models\User;
use App\Models\Staff;
use App\Notifications\InventoryExpirationNotification;
use Carbon\Carbon;

class CheckInventoryExpiration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory:check-expiration {days=30 : Days before expiration to send notification}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for inventory items that will expire soon and send notifications';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Cast days to integer to avoid Carbon type error
        $daysThreshold = (int) $this->argument('days');
        $targetDate = Carbon::now()->addDays($daysThreshold);

        // Get all inventory items expiring within the threshold
        $expiringItems = Inventory::whereNotNull('expiry_date')
            ->where('expiry_date', '<=', $targetDate)
            ->where('expiry_date', '>=', Carbon::now())
            ->where('is_active', true)
            ->get();

        if ($expiringItems->isEmpty()) {
            $this->info('No inventory items expiring soon.');
            return 0;
        }

        $this->info('Found ' . $expiringItems->count() . ' inventory items expiring soon.');

        // Get all admin users
        $adminUsers = User::whereHas('staff', function ($query) {
            $query->where('role', 'admin')
                  ->where('is_active', true);
        })->get();

        if ($adminUsers->isEmpty()) {
            $this->error('No admin users found to notify.');
            return 1;
        }

        foreach ($expiringItems as $item) {
            // Make sure this is an integer as well
            $daysRemaining = (int) Carbon::now()->diffInDays($item->expiry_date);

            foreach ($adminUsers as $admin) {
                // Check if we've already notified about this item (within the last 7 days)
                $existingNotification = $admin->notifications()
                    ->where('data->inventory_id', $item->id)
                    ->where('created_at', '>=', Carbon::now()->subDays(7))
                    ->exists();

                if (!$existingNotification) {
                    $admin->notify(new InventoryExpirationNotification($item, $daysRemaining));
                    $this->info("Notification sent to {$admin->name} about {$item->name} expiring in {$daysRemaining} days.");
                }
            }
        }

        return 0;
    }
}
