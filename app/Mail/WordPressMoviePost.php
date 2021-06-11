<?php

namespace App\Mail;

use App\Models\Movie;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\RateLimitedMiddleware\RateLimited;

class WordPressMoviePost extends Mailable implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Movie $movie;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Movie $movie)
    {
        $this->movie = $movie;
    }

    /**
     * Attempt 1: Release after 60 seconds
     * Attempt 2: Release after 180 seconds
     * Attempt 3: Release after 420 seconds
     * Attempt 4: Release after 900 seconds
     */
    public function middleware()
    {
        if (config('app.env') !== 'testing') {
            $rateLimitedMiddleware = (new RateLimited())
                ->allow(100) // Allow 100 jobs
                ->everyMinutes(1440)
                ->releaseAfterMinutes(1440); // Release back to pool after 1440 minutes

            return [$rateLimitedMiddleware];
        }

        return [];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->to(config('mail.to.address'), config('mail.to.name'))
            ->view('emails.movie')
            ->with([
                'movie' => $this->movie,
                'tags' => implode(', ', $this->movie->tags()->get(['name'])->keyBy('name')->keys()->toArray()),
                'idols' => implode(', ', $this->movie->idols()->get(['name'])->keyBy('name')->keys()->toArray()),
                'onejav' => $this->movie->onejav->first(),
            ]);
    }
}
