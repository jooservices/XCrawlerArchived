<?php

namespace App\Jav\Mail;

use App\Models\Movie;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class WordPressMoviePost extends Mailable implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Movie $movie;

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
