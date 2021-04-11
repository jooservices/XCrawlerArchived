<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string            $name
 * @property string            $cover
 * @property string            $content_id
 * @property string            $dvd_id
 * @property string            $description
 * @property bool              $is_downloadable
 * @property Onejav            $onejav
 */
class Movie extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'cover',
        'sales_date',
        'release_date',
        'content_id',
        'dvd_id',
        'description',
        'time',
        'director',
        'studio',
        'label',
        'channel',
        'series',
        'gallery',
        'sample',
        'is_downloadable',
    ];

    public function getFields(): array
    {
        return $this->fillable;
    }

    public function onejav()
    {
        return $this->belongsTo(Onejav::class, 'dvd_id', 'dvd_id');
    }
}
