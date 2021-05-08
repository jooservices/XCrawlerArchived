<?php

namespace App\Services\Crawler;

use App\Models\XCityIdol;
use App\Services\Client\XCrawlerClient;
use DateTime;
use Illuminate\Support\Collection;
use Symfony\Component\DomCrawler\Crawler;

class XCityIdolCrawler
{
    private XCrawlerClient $client;

    public function __construct()
    {
        $this->client = app(XCrawlerClient::class);
        $this->client->init('xcity_idol');
    }

    protected array $months = [
        'Jan' => '01',
        'Feb' => '02',
        'Mar' => '03',
        'Apr' => '04',
        'May' => '05',
        'Jun' => '06',
        'Jul' => '07',
        'Aug' => '08',
        'Sep' => '09',
        'Oct' => '10',
        'Nov' => '11',
        'Dec' => '12',
    ];

    public function getSubPages(): Collection
    {
        $response = $this->client->get(XCityIdol::HOMEPAGE_URL);

        if (!$response->isSuccessful()) {
            return collect();
        }

        $links = $response->getData()->filter('ul.itemStatus li a')->each(function (Crawler $node) {
            return $node->attr('href');
        });

        return collect($links);
    }

    public function getItem(string $url, array $payload = []): ?Item
    {
        $response = $this->client->get($url, $payload);

        if (!$response->isSuccessful()) {
            return null;
        }

        $item = app(Item::class);
        $item->url = $url;
        if ($response->getData()->filter('.itemBox h1')->count() === 0) {
            return null;
        }
        $item->name = $response->getData()->filter('.itemBox h1')->text(null, false);
        $item->cover = $response->getData()->filter('.photo p.tn img')->attr('src');
        $fields = collect($response->getData()->filter('#avidolDetails dl.profile dd')->each(
            function (Crawler $node) {
                $text = $node->text(null, false);
                if (str_contains($text, '★Favorite')) {
                    return ['favorite' => (int)str_replace('★Favorite', '', $text)];
                }
                if (str_contains($text, 'Date of birth')) {
                    $birthday = trim(str_replace('Date of birth', '', $text));
                    if (empty($birthday)) {
                        return null;
                    }
                    $days = explode(' ', $birthday);

                    if (!isset($this->months[$days[1]])) {
                        return null;
                    }

                    $month = $this->months[$days[1]];

                    return ['birthday' => DateTime::createFromFormat('Y-m-d', $days[0] . '-' . $month . '-' . $days[2])];
                }
                if (str_contains($text, 'Blood Type')) {
                    $bloodType = str_replace(['Blood Type', 'Type', '-', '_'], ['', '', '', ''], $text);
                    return ['blood_type' => trim($bloodType)];
                }
                if (str_contains($text, 'City of Born')) {
                    return ['city' => trim(str_replace('City of Born', '', $text))];
                }
                if (str_contains($text, 'Height')) {
                    return ['height' => trim(str_replace('cm', '', str_replace('Height', '', $text)))];
                }
                if (str_contains($text, 'Size')) {
                    $sizes = trim(str_replace('Size', '', $text));
                    if (empty($sizes)) {
                        return null;
                    }
                    $sizes = explode(' ', $sizes);
                    foreach ($sizes as $index => $size) {
                        switch ($index) {
                            case 0:
                                $size = str_replace('B', '', $size);
                                $size = explode('(', $size);
                                $breast = empty(trim($size[0])) ? null : (int)$size[0];
                                break;
                            case 1:
                                $size = str_replace('W', '', $size);
                                $size = explode('(', $size);
                                $waist = empty(trim($size[0])) ? null : (int)$size[0];
                                break;
                            case 2:
                                $size = str_replace('H', '', $size);
                                $size = explode('(', $size);
                                $hips = empty(trim($size[0])) ? null : (int)$size[0];
                                break;
                        }
                    }

                    return [
                        'breast' => $breast ?? null,
                        'waist' => $waist ?? null,
                        'hips' => $hips ?? null,
                    ];
                }

                return null;
            }
        ))->reject(static function ($value) {
            return null === $value;
        });

        // @TODO Use collection each
        foreach ($fields as $field) {
            foreach ($field as $key => $value) {
                $item->{$key} = empty($value) ? null : $value;
            }
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

        if (0 !== $response->getData()->filter('.itemBox p.tn')->count()) {
            $links = $response->getData()->filter('.itemBox p.tn')->each(static function ($el) {
                return $el->filter('a')->attr('href');
            });

            return collect($links);
        }

        return collect($response->getData()->filter('.itemBox p.name a')->each(static function ($el) {
            return $el->filter('a')->attr('href');
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

        return (int)$response->getData()->filter('ul.pageScrl li.next')->previousAll()->filter('li a')->text(null, false);
    }
}
