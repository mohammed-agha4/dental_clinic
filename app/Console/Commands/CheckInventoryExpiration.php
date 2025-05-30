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

    protected $signature = 'inventory:check-expiration {days=30 : Days before expiration to send notification}';

    protected $description = 'Check for inventory items that will expire soon and send notifications';

    public function handle()
    {
        $daysPara = (int) $this->argument('days'); // days: the parameter in the signature
        $targetDate = Carbon::now()->addDays($daysPara);

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

        $adminUsers = User::whereHas('staff', function ($query) {
            $query->where('role', 'admin')
                ->where('is_active', true);
        })->get();

        foreach ($expiringItems as $item) {
            $daysRemaining = (int) Carbon::now()->diffInDays($item->expiry_date); // difInDays: calculates difference in days between two dates.


            foreach ($adminUsers as $admin) {
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
