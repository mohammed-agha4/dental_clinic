<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Inventory;

class InventoryExpirationNotification extends Notification
{
    use Queueable;

    protected $inventory;
    protected $daysRemaining;

    /**
     * Create a new notification instance.
     *
     * @param Inventory $inventory
     * @param int $daysRemaining
     * @return void
     */
    public function __construct(Inventory $inventory, int $daysRemaining)
    {
        $this->inventory = $inventory;
        $this->daysRemaining = $daysRemaining;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'title' => 'Inventory Expiration Alert',
            'message' => "Item '{$this->inventory->name}' (SKU: {$this->inventory->SKU}) will expire in {$this->daysRemaining} days.",
            'inventory_id' => $this->inventory->id,
            'url' => route('dashboard.inventory.inventory.show', $this->inventory->id),
            'icon' => 'exclamation-triangle',
            'type' => 'expiration',
            'daysRemaining' => $this->daysRemaining
        ];
    }
}
