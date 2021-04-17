<?php

namespace App\Models;

use App\Models\Traits\HasEvents;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

/**
 * @property string $name
 * @property string $cover
 * @property string $content_id
 * @property string $dvd_id
 * @property string $description
 * @property bool $is_downloadable
 * @property Collection|Onejav[] $onejav
 */
class Movie extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Notifiable;
    use HasEvents;

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

    protected $casts = [
        'name' => 'string',
        'cover' => 'string',
        'content_id' => 'string',
        'dvd_id' => 'string',
        'description' => 'string',
        'label' => 'string',
        'channel' => 'string',
        'is_downloadable' => 'boolean',
    ];

    public function getFields(): array
    {
        return $this->fillable;
    }

    public function onejav()
    {
        return $this->hasMany(Onejav::class, 'dvd_id', 'dvd_id')->orderBy('size');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'tag_movie');
    }

    public function idols()
    {
        return $this->belongsToMany(Idol::class, 'idol_movie');
    }

    /**
     * Route notifications for the Slack channel.
     *
     * @param \Illuminate\Notifications\Notification $notification
     * @return string
     */
    public function routeNotificationForSlack($notification)
    {
        return config('services.slack.notifications');
    }
}
