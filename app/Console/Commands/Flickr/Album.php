<?php

namespace App\Console\Commands\Flickr;

use App\Jobs\Flickr\AlbumInfo;
use Illuminate\Console\Command;

class Album extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flickr:album {--nsid=} {--albumid=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch Flickr Album';

    public function handle()
    {
        AlbumInfo::dispatch($this->option('albumid'), $this->option('nsid'));
    }
}
