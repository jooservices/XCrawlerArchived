<?php

namespace App\Jobs\Email;

use App\Jobs\Traits\HasUnique;
use App\Models\Movie;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class WordPress implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use HasUnique;

    private Movie $movie;

    public function __construct(Movie $movie)
    {
        $this->movie = $movie;
    }

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId(): string
    {
        return $this->getUnique([$this->movie->dvd_id]);
    }

    /**
     * Determine the time at which the job should timeout.
     *
     * @return \DateTime
     */
    public function retryUntil()
    {
        return now()->addHours(24);
    }

    public function handle()
    {
        $tags = $this->movie->tags()->get()->keyBy('name')->keys();
        $tags->merge($this->movie->idols()->get()->keyBy('name')->keys());
        Mail::send(
            'emails.mail',
            [
                'title' => $this->movie->dvd_id,
                'tags' => implode(', ', $tags->toArray()),
                'description' => $this->movie->description,
                'cover' => $this->movie->cover,
            ],
            function ($message) {
                $message->to(config('mail.to.address'), config('mail.to.name'))->subject($this->movie->dvd_id);
                $message->from(config('mail.from.address'), config('mail.from.name'));
            });
    }
}
