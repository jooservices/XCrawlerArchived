<?php

namespace App\Services\Crawler;

use App\Services\Client\XCrawlerClient;
use DateTime;
use Illuminate\Support\Collection;

class XCityVideoCrawler
{
    private XCrawlerClient $client;

    public function __construct()
    {
        $this->client = app(XCrawlerClient::class);
        $this->client->init('xcity_video');
    }

    public function getItem(string $url, array $payload = []): ?Item
    {
        $response = $this->client->get($url, $payload);

        if (!$response->isSuccessful()) {
            return null;
        }

        $item = app(Item::class);
        $item->url = $url;
        $item->name = $response->getData()->filter('#program_detail_title')->text(null, false);
        $item->cover = $response->getData()->filter('div.photo a')->attr('href');
        $item->gallery = collect($response->getData()->filter('img.launch_thumbnail')->each(static function ($el) {
            return $el->attr('src');
        }))->unique()->toArray();

        $item->actresses = collect(
            $response->getData()->filter('.bodyCol ul li.credit-links a')->each(static function ($el) {
                return trim($el->text());
            })
        )->unique()->toArray();

        // Get all fields
        $fields = collect($response->getData()->filter('.bodyCol ul li')->each(
            function ($li) {
                $node = $li->filter('.koumoku');
                if (0 == $node->count()) {
                    return [];
                }

                $label = $node->text();

                switch ($label) {
                    case '★Favorite':
                        return ['favorite' => (int)$li->filter('.favorite-count')->text()];
                    case 'Sales Date':
                        return [
                            'sales_date' => DateTime::createFromFormat(
                                'Y/m/j',
                                trim(str_replace('Sales Date', '', $li->text()))
                            ),
                        ];
                    case 'Label/Maker':
                        return [
                            'label' => $li->filter('#program_detail_maker_name')->text(),
                            'marker' => $li->filter('#program_detail_label_name')->text(),
                        ];
                    case 'Series':
                        return ['series' => trim(str_replace('Series', '', $li->text()))];
                    case 'Genres':
                        $genres = $li->filter('a.genre')->each(
                            static function ($a) {
                                return trim($a->text(null, false));
                            }
                        );

                        return ['tags' => $genres];
                    case 'Director':
                        $node = $li->filter('#program_detail_director');

                        return ['director' => $node->count() > 0 ? $node->text() : null];
                    case 'Item Number':
                        return ['item_number' => trim($li->filter('#hinban')->text())];
                    case 'Running Time':
                        return [
                            'time' => (int)trim(str_replace(
                                ['Running Time', 'min', '.'],
                                ['', '', ''],
                                $li->text(null, false)
                            )),
                        ];

                    case 'Release Date':
                        $releaseDate = trim(str_replace('Release Date', '', $li->text(null, false)));
                        if (!empty($releaseDate) && !str_contains($releaseDate, 'undelivered now')) {
                            return ['release_date' => DateTime::createFromFormat('Y/m/j', $releaseDate)];
                        }
                }

                return null;
            }
        ))->reject(static function ($value) {
            return null === $value;
        })->toArray();

        foreach ($fields as $field) {
            foreach ($field as $key => $value) {
                if ('item_number' === $key) {
                    $item->{$key} = empty($value) ? null : $value;

                    $value = implode('-', preg_split("/(,?\s+)|((?<=[a-z])(?=\d))|((?<=\d)(?=[a-z]))/i", $value));
                    $item->dvd_id = empty($value) ? null : $value;

                    continue;
                }
                $item->{$key} = empty($value) ? null : $value;
            }
        }

        $item->description = $response->getData()->filter('p.lead')->text();

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

        return collect($response->getData()->filter('.x-itemBox')->each(static function ($el) {
            return 'https://xxx.xcity.jp' . $el->filter('.x-itemBox-package a')->attr('href');
        }));
    }

    public function getPages(string $url, array $payload = []): int
    {
        $response = $this->client->get($url, $payload);

        if (!$response->isSuccessful()) {
            return 1;
        }

        $nodes = $response->getData()->filter('ul.pageScrl li.next');

        if (0 === $nodes->count() || 0 === $nodes->previousAll()->filter('li a')->count()) {
            return 1;
        }

        return (int)$response->getData()
            ->filter('ul.pageScrl li.next')->previousAll()
            ->filter('li a')
            ->text(null, false);
    }
}
