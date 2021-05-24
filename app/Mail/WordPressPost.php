<?php

namespace App\Mail;

use App\Models\Movie;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WordPressPost extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

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
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->to(config('mail.to.address'), config('mail.to.name'))
            ->view('emails.mail')
            ->with([
                'title' => $this->movie->dvd_id,
                'tags' => implode(', ', $this->movie->tags()->get(['name'])->keyBy('name')->keys()->toArray()),
                'idols' => implode(', ', $this->movie->idols()->get(['name'])->keyBy('name')->keys()->toArray()),
                'onejav' => $this->movie->onejav->first(),
                'description' => $this->movie->description,
                'cover' => $this->movie->cover,
            ]);
    }
}
