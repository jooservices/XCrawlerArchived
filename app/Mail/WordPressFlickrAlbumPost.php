<?php

namespace App\Mail;

use App\Models\FlickrDownload;
use App\Models\FlickrDownloadItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WordPressFlickrAlbumPost extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    private FlickrDownload $download;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(FlickrDownload $download)
    {
        $this->download = $download;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $items = $this->download->items();
        $urls = [];
        $items->each(function ($item) use (&$urls) {
            /**
             * @var FlickrDownloadItem $item
             */
            $photo = $item->photo;
            $urls[] = $photo->largestSize()['source'];
        });

        return $this
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->to(config('mail.to.address'), config('mail.to.name'))
            ->view('emails.flickr.album')
            ->with([
                'album' => $this->download->name,
                'urls' => $urls
            ]);
    }
}
