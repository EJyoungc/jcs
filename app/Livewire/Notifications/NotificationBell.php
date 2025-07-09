<?php

namespace App\Livewire\Notifications;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationBell extends Component
{
    public $unreadNotifications;
    public $unreadNotificationsCount;

    protected $listeners = ['notification' => 'mount'];

    public function mount()
    {
        if (Auth::check()) {
            $this->unreadNotifications = Auth::user()->unreadNotifications;
            $this->unreadNotificationsCount = $this->unreadNotifications->count();
        }
    }

    public function markAsRead($notificationId)
    {
        if (Auth::check()) {
            $notification = Auth::user()->notifications()->find($notificationId);
            if ($notification) {
                $notification->markAsRead();
            }
            $this->mount(); // Refresh the component
        }
    }

    public function render()
    {
        return view('livewire.notifications.notification-bell');
    }
}