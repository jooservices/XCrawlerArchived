<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $url
 * @property string $cover
 * @property string $dvd_id
 * @property float $size
 * @property array $tags
 * @property array $actresses
 * @property string $description
 * @property string $torrent
 */
class Onejav extends AbstractJavMovie
{
    use HasFactory;
    use SoftDeletes;

    const HOMEPAGE_URL = 'https://onejav.com';
    const NEW_URL = self::HOMEPAGE_URL . '/new';
    const DAILY_FORMAT = 'Y/m/d';

    protected $fillable = [
        'url',
        'cover',
        'dvd_id',
        'size',
        'date',
        'tags',
        'description',
        'actresses',
        'torrent',
    ];

    protected $casts = [
        'url' => 'string',
        'cover' => 'string',
        'dvd_id' => 'string',
        'size' => 'float',
        'date' => 'datetime:Y-m-d',
        'tags' => 'array',
        'description' => 'string',
        'actresses' => 'array',
        'torrent' => 'string',
        'updated_at' => 'datetime:Y-m-d H:m:s',
        'created_at' => 'datetime:Y-m-d H:m:s',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'deleted_at',
    ];

    protected $table = 'onejav';

    public function isDownloadable(): bool
    {
        return true;
    }
}
