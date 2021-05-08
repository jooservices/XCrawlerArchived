<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackAttachment;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

/**
 * Send notifications on favorited movies
 * @package App\Notifications
 */
class FavoritedMovie extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['slack', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }

    /**
     * Get the Slack representation of the notification.
     *
     * @param mixed $notifiable
     * @return SlackMessage
     */
    public function toSlack($notifiable)
    {
        return (new SlackMessage)
            ->success()
            ->content($notifiable->dvd_id)
            ->attachment(function (SlackAttachment $attachment) use ($notifiable) {
                if (!empty($notifiable->description)) {
                    $attachment->content($notifiable->description);
                }

                if ($notifiable->cover) {
                    $attachment->image($notifiable->cover);
                }

                if ($onejav = $notifiable->onejav()->first()) {
                    $url = '<' . $onejav->url . '|' . $onejav->url . '>';
                    $attachment->footer($url);
                }

                $attachment->fields([
                    'Categories' => implode(', ', $notifiable->tags()->pluck('name')->toArray()),
                    'Idols' => implode(', ', $notifiable->idols()->pluck('name')->toArray()),
                ]);
            });
    }
}
