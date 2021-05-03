<?php

namespace App\Services;

use App\Jobs\R18FetchItemJob;
use App\Models\R18;
use App\Models\TemporaryUrl;
use App\Services\Crawler\R18Crawler;

class R18Service
{
    public const SOURCE = 'r18';

    public function released()
    {
        $crawler = app(R18Crawler::class);
        if (!$temporary = TemporaryUrl::forSource(self::SOURCE)->forState(TemporaryUrl::STATE_INIT)->first()) {
            $temporary = TemporaryUrl::create([
                'url' => R18::MOVIE_LIST_URL,
                'source' => self::SOURCE, // For R18 we only save 1 temporary
                'data' => [
                    'pages' => $crawler->getPages(R18::MOVIE_LIST_URL),
                    'current_page' => 1
                ],
                'state_code' => TemporaryUrl::STATE_INIT
            ]);
        }

        $currentPage = (int)$temporary->data['current_page'];
        $url = R18::MOVIE_LIST_URL . '/page=' . $currentPage;
        $links = $crawler->getItemLinks($url);
        $links->each(function ($link) {
            R18FetchItemJob::dispatch($link, 'r18.released');
        });

        if ($currentPage === (int)$temporary->data['pages']) {
            return $temporary->completed();
        }

        $currentPage++;
        $temporary->updateData(['current_page' => $currentPage]);
    }
}
