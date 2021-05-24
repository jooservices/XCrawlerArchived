<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackAttachment;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;
use Spatie\RateLimitedMiddleware\RateLimited;
use Throwable;

class ExceptionNotifcation extends Notification implements ShouldQueue
{
    use Queueable;

    private Throwable $exception;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct( Throwable $exception)
    {
        $this->exception = $exception;
    }

    /**
     * Attempt 1: Release after 60 seconds
     * Attempt 2: Release after 180 seconds
     * Attempt 3: Release after 420 seconds
     * Attempt 4: Release after 900 seconds
     */
    public function middleware()
    {
        $rateLimitedMiddleware = (new RateLimited())
            ->allow(3) // Allow 3 jobs
            ->everySecond()
            ->releaseAfterMinutes(1); // Release back to pool after 60 seconds

        return [$rateLimitedMiddleware];
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['slack'];
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
            'File' => $this->exception->getFile(),
            'Line' => $this->exception->getLine(),
        ];
    }

    public function toSlack($notifiable)
    {
        if (app()->environment('production')) {
            return (new SlackMessage)
                ->from(config('app.name') )
                ->content($this->exception->getMessage())
                ->attachment(function (SlackAttachment $attachment) {
                    $attachment->fields([
                        'File' => $this->exception->getFile(),
                        'Line' => $this->exception->getLine(),
                    ]);
                });
        }
    }
}
