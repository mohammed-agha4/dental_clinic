<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NotificationsController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()->notifications()->paginate(10);
        return view('dashboard.notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->markAsRead();

            // Redirect based on notification type
            if (isset($notification->data['url'])) {
                return redirect($notification->data['url']);
            } else if (Str::contains($notification->type, 'ReorderNotification') && isset($notification->data['inventory_id'])) {
                return redirect()->route('inventories.show', $notification->data['inventory_id']);
            } else if (Str::contains($notification->type, 'NewAppointmentNotification') && isset($notification->data['appointment_id'])) {
                return redirect()->route('appointments.show', $notification->data['appointment_id']);
            }
        }

        return redirect()->route('dashboard');
    }

    public function markAllRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return back()->with('success', 'All notifications marked as read');
    }

    public function deleteNotification($id)
    {
        $notification = auth()->user()->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->delete();
            return back()->with('success', 'Notification deleted successfully');
        }

        return back()->with('error', 'Notification not found');
    }

    public function deleteAllNotifications()
    {
        auth()->user()->notifications()->delete();
        return back()->with('success', 'All notifications deleted successfully');
    }
}
