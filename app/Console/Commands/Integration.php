<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Jooservices\PhpFlickr\PhpFlickr;
use OAuth\Common\Storage\Memory;

class Integration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'integration {service=flickr}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Integration with 3rd pary';

    public function handle()
    {
        switch ($this->argument('service')) {
            case 'flickr':
                $flickr = new PhpFlickr(config('services.flickr.client_id'), config('services.flickr.client_secret'));
                $storage = app(Memory::class);
                $flickr->setOauthStorage($storage);
                $perm = 'read';

                $url = $flickr->getAuthUrl($perm);

                $this->output->text($url->getAbsoluteUri());
                $code = $this->output->ask('Enter code');
                $accessToken = $flickr->retrieveAccessToken($code);

                \App\Models\Integration::updateOrCreate([
                    'service' => 'flickr',
                ], [
                    'token_secret' => $accessToken->getAccessTokenSecret(),
                    'token' => $accessToken->getAccessToken(),
                    'data' => $accessToken
                ]);
                break;
        }
    }
}
