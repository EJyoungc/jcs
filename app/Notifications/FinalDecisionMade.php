<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Application;

class FinalDecisionMade extends Notification
{
    use Queueable;

    public $applicationId;
    public $status;

    /**
     * Create a new notification instance.
     */
    public function __construct(int $applicationId, string $status)
    {
        $this->applicationId = $applicationId;
        $this->status = $status;
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
                    ->line('A final decision has been made on an application.')
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
            'message' => 'The final decision on application #' . $this->applicationId . ' is: ' . $this->status,
            'url' => route('application-review', ['id' => $this->applicationId]),
        ];
    }
}