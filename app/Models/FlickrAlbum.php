<?php

namespace App\Models;

use App\Models\Traits\HasSlackNotifications;
use App\Models\Traits\HasStates;
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
    use HasStates;

    public const STATE_INIT = 'FAIN';
    public const STATE_INFO_FAILED = 'FAIF';
    public const STATE_PHOTOS_PROCESSING = 'FAPP';
    public const STATE_PHOTOS_COMPLETED = 'FAPC';
    public const STATE_PHOTOS_FAILED = 'FAPF';

    public const STATES = [
        self::STATE_INIT,
        self::STATE_INFO_FAILED,
        self::STATE_PHOTOS_PROCESSING,
        self::STATE_PHOTOS_COMPLETED,
        self::STATE_PHOTOS_FAILED
    ];

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

    public function owner()
    {
        return $this->belongsTo(FlickrContact::class, 'owner', 'nsid');
    }
}
