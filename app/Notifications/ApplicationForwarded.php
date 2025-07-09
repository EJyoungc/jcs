<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Application;

class ApplicationForwarded extends Notification
{
    use Queueable;

    public $applicationId;
    public $message;

    /**
     * Create a new notification instance.
     */
    public function __construct(int $applicationId, string $message)
    {
        $this->applicationId = $applicationId;
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line($this->message)
                    ->action('View Application', route('application-review', ['id' => $this->applicationId]))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'application_id' => $this->applicationId,
            'message' => $this->message,
            'url' => route('application-review', ['id' => $this->applicationId]),
        ];
    }
}