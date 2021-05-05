<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $url
 * @property string $cover
 * @property string $title
 * @property string $release_date
 * @property integer $runtime
 * @property string $director
 * @property string $studio
 * @property string $label
 * @property string $channel
 * @property string $content_id
 * @property string $dvd_id
 * @property string $series
 * @property string $languages
 * @property string $sample
 * @property array $gallery
 * @package App\Models
 */
class R18 extends AbstractJavMovie
{
    use HasFactory;
    use SoftDeletes;

    const HOMEPAGE_URL = 'https://www.r18.com';
    const MOVIE_LIST_URL = self::HOMEPAGE_URL . '/videos/vod/movies/list/pagesize=30/price=all/sort=new/type=all';

    protected $table = 'r18';

    protected $fillable = [
        'url',
        'cover',
        'title',
        'release_date',
        'runtime',
        'director',
        'studio',
        'label',
        'channel',
        'content_id',
        'dvd_id',
        'series',
        'languages',
        'sample',
        'gallery',
        'tags',
        'actresses',
    ];

    protected $casts = [
        'url' => 'string',
        'cover' => 'string',
        'title' => 'string',
        'dvd_id' => 'string',
        'release_date' => 'datetime:Y-m-d',
        'tags' => 'array',
        'actresses' => 'array',
        'gallery' => 'array',
        'updated_at' => 'datetime:Y-m-d H:m:s',
        'created_at' => 'datetime:Y-m-d H:m:s',
    ];

    protected $hidden = [
        'updated_at',
        'created_at',
        'deleted_at',
    ];

    public function isDownloadable(): bool
    {
        return false;
    }

    public function getName(): ?string
    {
        return $this->title;
    }
}
