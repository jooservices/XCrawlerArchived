<?php

namespace App\Services\Crawler;

use App\Models\Onejav;
use App\Services\Client\XCrawlerClient;
use Illuminate\Support\Collection;
use Symfony\Component\DomCrawler\Crawler;

class OneJavCrawler
{
    private XCrawlerClient $client;

    public function __construct()
    {
        $this->client = app(XCrawlerClient::class);
        $this->client->init('onejav');
    }

    public function getItems(string $url): ?Collection
    {
        $response = $this->client->get($url);

        if (!$response->isSuccessful()) {
            return null;
        }

        return collect($response->getData()->filter('.container .columns')->each(function ($el) {
            return $this->parse($el);
        }));
    }

    private function parse(Crawler $crawler): Item
    {
        $item = app(Item::class);
        $item->url = Onejav::HOMEPAGE_URL.trim($crawler->filter('h5.title a')->attr('href'));

        if ($crawler->filter('.columns img.image')->count()) {
            $item->cover = trim($crawler->filter('.columns img.image')->attr('src'));
        }

        if ($crawler->filter('h5 a')->count()) {
            $item->dvd_id = (trim($crawler->filter('h5 a')->text(null, false)));
            $item->dvd_id = implode('-', preg_split("/(,?\s+)|((?<=[a-z])(?=\d))|((?<=\d)(?=[a-z]))/i", $item->dvd_id));
        }

        if ($crawler->filter('h5 span')->count()) {
            $item->size = trim($crawler->filter('h5 span')->text(null, false));

            if (str_contains($item->size, 'MB')) {
                $item->size = (float) trim(str_replace('MB', '', $item->size));
                $item->size = $item->size / 1024;
            } elseif (str_contains($item->size, 'GB')) {
                $item->size = (float) trim(str_replace('GB', '', $item->size));
            }
        }

        $item->date = $this->convertStringToDateTime(trim($crawler->filter('.subtitle.is-6 a')->attr('href')));
        $item->tags = collect($crawler->filter('.tags .tag')->each(
            function ($tag) {
                return trim($tag->text(null, false));
            }
        ))->reject(function ($value) {
            return null === $value || empty($value);
        })->unique()->toArray();

        $description = $crawler->filter('.level.has-text-grey-dark');
        $item->description = $description->count() ? trim($description->text(null, false)) : null;
        $item->description = preg_replace("/\r|\n/", '', $item->description);

        $item->actresses = collect($crawler->filter('.panel .panel-block')->each(
            function ($actress) {
                return trim($actress->text(null, false));
            }
        ))->reject(function ($value) {
            return null === $value || empty($value);
        })->unique()->toArray();

        $item->torrent = Onejav::HOMEPAGE_URL.trim($crawler->filter('.control.is-expanded a')->attr('href'));

        return $item;
    }

    private function convertStringToDateTime(string $date): ?\DateTime
    {
        try {
            $date = trim($date, '/');
            if (!$dateTime = \DateTime::createFromFormat('Y/m/j', $date)) {
                return null;
            }

            return $dateTime;
        } catch (\Exception $exception) {
            return null;
        }
    }
}
