<?php

namespace App\Livewire\Notifications;

use Illuminate\Notifications\DatabaseNotification;
use App\Models\AuditLog;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class NotificationsLivewire extends Component
{
    use LivewireAlert;

    public $notifications;

    protected $listeners = ['refreshNotifications' => '$refresh'];

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $this->notifications = Auth::user()->notifications()
                                ->orderBy('read_at', 'asc')
                                ->orderBy('created_at', 'desc')
                                ->get();
    }

    public function markAsRead(DatabaseNotification $notification)
    {
        if (is_null($notification->read_at)) {
            $notification->markAsRead();
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'Notification read',
                'description' => 'Notification ' . $notification->id . ' marked as read.',
                'auditable_type' => DatabaseNotification::class,
                'auditable_id' => $notification->id,
            ]);
            $this->alert('success', 'Notification marked as read.');
            $this->loadNotifications();
        }
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'All notifications read',
            'description' => 'All unread notifications marked as read.',
            'auditable_type' => DatabaseNotification::class,
            'auditable_id' => null,
        ]);
        $this->alert('success', 'All notifications marked as read.');
        $this->loadNotifications();
    }

    public function render()
    {
        return view('livewire.notifications.notifications-livewire');
    }
}
