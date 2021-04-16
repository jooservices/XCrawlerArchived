<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class R18 extends AbstractJavMovie
{
    use HasFactory;
    use SoftDeletes;

    const HOMEPAGE_URL = 'https://www.r18.com';
    const MOVIE_LIST_URL = self::HOMEPAGE_URL . '/videos/vod/movies/list/pagesize=120/price=all/sort=new/type=all';

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

    public function getDvdId(): string
    {
        return $this->dvd_id;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function getActresses(): array
    {
        return $this->actresses;
    }
}
