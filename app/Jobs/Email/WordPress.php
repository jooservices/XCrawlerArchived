<?php

namespace App\Jobs\Email;

use App\Jobs\Traits\HasUnique;
use App\Models\Movie;
use App\Models\WordPressPost;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Spatie\RateLimitedMiddleware\RateLimited;

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
     * Attempt 1: Release after 60 seconds
     * Attempt 2: Release after 180 seconds
     * Attempt 3: Release after 420 seconds
     * Attempt 4: Release after 900 seconds
     */
    public function middleware()
    {
        if (config('app.env') !== 'testing') {
            $rateLimitedMiddleware = (new RateLimited())
                ->allow(4) // Allow 4 jobs
                ->everySecond() // In second
                ->releaseAfterMinutes(15); // Release back to pool after 30 minutes

            return [$rateLimitedMiddleware];
        }

        return [];
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
        if (WordPressPost::where(['title' => $this->movie->dvd_id])->exists()) {
            return;
        }
        Mail::send(new \App\Mail\WordPressPost($this->movie));
        WordPressPost::create(['title' => $this->movie->dvd_id]);
    }
}
