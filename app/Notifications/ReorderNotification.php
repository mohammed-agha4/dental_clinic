<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use App\Models\Inventory;

class ReorderNotification extends Notification
{
    protected $inventory;

    public function __construct(Inventory $inventory)
    {
        $this->inventory = $inventory;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => "Low stock alert: {$this->inventory->name} (SKU: {$this->inventory->SKU})",
            'quantity' => $this->inventory->quantity,
            'reorder_level' => $this->inventory->reorder_level,
            'url' => route('notifications.index', $this->inventory->id),
            'icon' => 'fas fa-boxes',
            'inventory_id' => $this->inventory->id
        ];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'inventory_id' => $this->inventory->id,
            'message' => "Low stock level for {$this->inventory->name}",
        ];
    }
}
