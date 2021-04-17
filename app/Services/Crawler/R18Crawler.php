<?php

namespace App\Services\Crawler;

use App\Services\Client\XCrawlerClient;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class R18Crawler
{
    private XCrawlerClient $client;

    public function __construct()
    {
        $this->client = app(XCrawlerClient::class);
        $this->client->init('r18');
    }

    public function getItem(string $url, array $payload = []): ?Item
    {
        $response = $this->client->get($url, $payload);

        if (!$response->isSuccessful()) {
            return null;
        }

        $item = app(Item::class);
        $item->url = $url;

        $item->cover = trim($response->getData()->filter('.detail-single-picture img')->attr('src'));
        $item->title = trim($response->getData()->filter('.product-details-page h1')->text(null, false));
        $item->tags = collect($response->getData()->filter('.product-categories-list a')->each(
            function ($el) {
                return trim($el->text(null, false));
            }
        ))->reject(function ($value) {
            return null === $value || empty($value);
        })->toArray();

        $fields = collect($response->getData()->filter('.product-onload .product-details dt')->each(
            function ($dt) {
                $text = trim($dt->text(null, false));
                $value = $dt->nextAll()->text(null, false);

                return [strtolower(str_replace(' ', '_', str_replace([':'], [''], $text))) => trim($value)];
            }
        ))->reject(function ($value) {
            return null === $value || empty($value);
        })->toArray();

        foreach ($fields as $field) {
            foreach ($field as $key => $value) {
                switch ($key) {
                    case 'release_date':
                        try {
                            $date = trim($value, '/');
                            $dateTime = null;

                            if (!$dateTime = Carbon::createFromFormat('M. d, Y', $date)) {
                                if (!$dateTime = Carbon::createFromFormat('M d, Y', $date)) {
                                    $dateTime = null;
                                }
                            }

                            $value = $dateTime;
                        } catch (\Exception $exception) {
                            break;
                        }
                        break;
                    default:
                        if ('----' === $value) {
                            $value = null;
                        }
                        break;
                }

                $item->{$key} = empty($value) ? null : $value;
            }
        }

        $item->actresses = collect($response->getData()->filter('.product-actress-list a span')->each(
            function ($span) {
                return trim($span->text(null, false));
            }
        ))->reject(function ($value) {
            return null === $value || empty($value);
        })->toArray();

        if ($response->getData()->filter('a.js-view-sample')->count()) {
            $item->sample = $response->getData()->filter('a.js-view-sample')->attr('data-video-high');
        }

        $item->gallery = collect($response->getData()->filter('.product-gallery a img.lazy')->each(function ($img) {
            return $img->attr('data-original');
        }))->toArray();

        if (isset($item->runtime) && !is_int($item->runtime)) {
            $item->runtime = (int)$item->runtime;
        }

        return $item;
    }

    /**
     * @param string $url
     * @param array $payload
     * @return Collection
     */
    public function getItemLinks(string $url, array $payload = []): Collection
    {
        $response = $this->client->get($url, $payload);

        if (!$response->isSuccessful()) {
            return collect();
        }

        return collect($response->getData()->filter('.main .cmn-list-product01 li.item-list a')->each(
            function ($el) {
                if (null === $el->attr('href')) {
                    return false;
                }

                $url = explode('?', $el->attr('href'));

                return $url[0];
            }
        ))->reject(function ($value) {
            return false === $value;
        })->unique();
    }

    /**
     * @param string $url
     * @param array $payload
     * @return int
     */
    public function getPages(string $url, array $payload = []): int
    {
        $response = $this->client->get($url, $payload);

        if (!$response->isSuccessful()) {
            return 1;
        }

        try {
            return (int)$response->getData()->filter('li.next')->previousAll()->filter('a')->text();
        } catch (Exception $exception) {
            return 1;
        }
    }
}
