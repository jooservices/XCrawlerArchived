<?php

namespace App\Flickr\Console\Commands;

use App\Flickr\Jobs\DownloadJob;
use Illuminate\Console\Command;

class Download extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flickr:download {type} {url} {--toWordPress}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download Flickr photo';

    public function handle()
    {
        DownloadJob::dispatch($this->argument('url'), $this->argument('type'), (bool)$this->option('toWordPress'));
    }
}
