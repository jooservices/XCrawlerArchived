<?php

namespace App\Mail;

use App\Models\Idol;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WordPressIdolPost extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    private Idol $idol;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Idol $idol)
    {
        $this->idol = $idol;
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
            ->view('emails.idol')
            ->with(['idol' => $this->idol]);
    }
}
