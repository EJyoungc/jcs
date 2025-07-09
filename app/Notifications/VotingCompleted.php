<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Application;

class VotingCompleted extends Notification
{
    use Queueable;

    public $applicationId;

    /**
     * Create a new notification instance.
     */
    public function __construct(int $applicationId)
    {
        $this->applicationId = $applicationId;
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
                    ->line('Voting has been completed for an application.')
                    ->action('View Application', route('recommendation-review', ['id' => $this->applicationId]))
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
            'message' => 'Voting has been completed for application #' . $this->applicationId . '. It is ready for your review.',
            'url' => route('recommendation-review', ['id' => $this->applicationId]),
        ];
    }
}