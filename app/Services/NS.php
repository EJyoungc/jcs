<?php

namespace App\Services;

use App\Models\Notification;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;

class NS
{
    /**
     * Create a notification for a user.
     *
     * 
     * @param string          $type        The notification type identifier
     * @param \Illuminate\Database\Eloquent\Model $notifiable The related model (morphable)
     * @param string          $message     The notification message
     * @return Notification
     */
    public static function create($id, string $type, $notifiable, string $message)
    {
        $id = $id ?? Auth::user()->id;

        return Notification::create([
            'user_id'      => $id,
            'type'         => $type,
            'notifiable_id'   => $notifiable->getKey(),
            'notifiable_type' => get_class($notifiable),
            'message'      => $message,
        ]);
    }
}
