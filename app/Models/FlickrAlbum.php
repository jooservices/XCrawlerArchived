<?php

namespace App\Models;

use App\Models\Traits\HasSlackNotifications;
use App\Models\Traits\HasState;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $owner
 * @property int    $primary
 * @property string $secret
 * @property int    $server
 * @property int    $farm
 * @property int    $photos
 * @property string $title
 * @property string $description
 * @property string $google_album_id
 */
class FlickrAlbum extends Model
{
    use HasFactory;
    use HasState;

    public const STATE_INIT = 'FAIN';
    public const STATE_PHOTOS_PROCESSING = 'FAPP';
    public const STATE_PHOTOS_COMPLETED = 'FAPC';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    protected $fillable = [
        'id',
        'owner',
        'primary',
        'secret',
        'server',
        'farm',
        'photos',
        'title',
        'description',
        'google_album_id',
        'state_code'
    ];

    public function photos()
    {
        return $this->belongsToMany(FlickrPhoto::class, 'flickr_photo_album', 'album_id', 'photo_id')->withTimestamps();
    }
}
