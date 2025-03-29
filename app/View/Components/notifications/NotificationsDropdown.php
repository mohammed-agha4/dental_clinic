<?php

namespace App\View\Components\notifications;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class NotificationsDropdown extends Component
{
    /**
     * Create a new component instance.
     */
    public $maxNotifications;

    public function __construct($maxNotifications = 5)
    {
        $this->maxNotifications = $maxNotifications;
        
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.notifications.notifications-dropdown', [
            'maxNotifications' => $this->maxNotifications
        ]);
    }
}
