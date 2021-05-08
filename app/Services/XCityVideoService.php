<?php

namespace App\Services;

use App\Models\TemporaryUrl;
use App\Services\Crawler\XCityVideoCrawler;
use Illuminate\Support\Carbon;

class XCityVideoService extends AbstractJavService
{
    public const SOURCE = 'xcity_videos';
    public const SOURCE_VIDEO = 'xcity_video';

    public function released()
    {
        $temporaryUrl = TemporaryUrl::forSource(self::SOURCE)->forState(TemporaryUrl::STATE_INIT)->first();
        $crawler = app(XCityVideoCrawler::class);

        if (!$temporaryUrl) {
            $temporaryUrl = TemporaryUrl::create([
                'url' => 'https://xxx.xcity.jp/avod/list/',
                'source' => self::SOURCE,
                'state_code' => TemporaryUrl::STATE_INIT,
                'data' => [
                    'from_date' => '20100101',
                    'page' => 1,
                ]
            ]);
        }

        $data = $temporaryUrl->data;
        $fromDate = Carbon::createFromFormat('Ymd', $temporaryUrl->data['from_date']);
        $payload = [
                'style' => 'simple',
                'to_date' => $fromDate->addDay()->format('Ymd'),
            ]
            + $data;

        $links = $crawler->getItemLinks('https://xxx.xcity.jp/avod/list/', $payload);
        $links->each(function ($url) {
            $query = parse_url($url, PHP_URL_QUERY);
            $url = str_replace('?' . $query, '', $url);
            parse_str($query, $query);
            TemporaryUrl::firstOrCreate([
                'url' => $url,
                'source' => XCityVideoService::SOURCE_VIDEO,
                'data' => [
                    'payload' => $query,
                ],
                'state_code' => TemporaryUrl::STATE_INIT
            ]);
        });

        $pages = $crawler->getPages('https://xxx.xcity.jp/avod/list/', $payload);
        if ($pages === $temporaryUrl->data['page']) {
            $temporaryUrl->completed();

            return;
        }

        $data['from_date'] = $fromDate->addDay()->format('Ymd');
        $data['page'] = $data['page'] + 1;
        $temporaryUrl->update(['data' => $data]);
    }
}
