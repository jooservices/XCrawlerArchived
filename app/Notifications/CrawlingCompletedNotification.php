<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\SlackAttachment;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;
use Spatie\RateLimitedMiddleware\RateLimited;

/**
 * Send notification after crawling completed
 * @package App\Notifications
 */
class CrawlingCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private string $service, private array $data)
    {
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

    public function toSlack($notifiable)
    {
        return (new SlackMessage)
            ->from(config('app.name') . ' | ' . strtoupper(config('app.env')))
            ->content($this->service . ' completed')
            ->attachment(function (SlackAttachment $attachment) {
                $attachment->title = $this->data['title'] ?? null;
                $attachment->content = $this->data['message'] ?? null;
                $attachment->footer = $this->data['footer'] ?? null;
            });

    }
}
