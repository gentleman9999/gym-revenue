<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GymRevNotification extends Notification implements ShouldBroadcast
{
    use Queueable;
    public readonly array $notification_data;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(public readonly int $user_id, array $payload)
    {
        // we are wrapping this is a "payload" because laravel have some reserved keys, e.g
        // "id" and "type", and so type and id gets overridden if specified.
        $this->notification_data = ['payload' => $payload];
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param object $_
     *
     * @return array
     */
    public function via(object $_): array
    {
        //TODO: look at $notifiable (user) and figure out which channel to broadcast on based on their preferences.
        return ['broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param object $_
     *
     * @return MailMessage
     */
    public function toMail(object $_): MailMessage
    {
        return (new MailMessage())
                    ->line('You\'ve got a generic notification!')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using Gym Revenue!');
    }

    /**
     * @param object $_
     *
     * @return BroadcastMessage
     */
    public function toBroadcast(object $_): BroadcastMessage
    {
        return new BroadcastMessage($this->notification_data);
    }
}
