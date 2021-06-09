<?php

namespace App\Models;

use App\Models\Traits\HasStates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read  FlickrPhoto $photo
 * @property-read  FlickrDownload $download
 * @package App\Models
 */
class FlickrDownloadItem extends Model
{
    use HasFactory;
    use HasStates;

    public const STATE_INIT = 'FDIN';
    public const STATE_WORDPRESS_INIT = 'FDWI';
    public const STATE_COMPLETED = 'FDCE';
    public const STATE_FAILED = 'FDFL';

    protected $fillable = [
        'download_id',
        'photo_id',
        'state_code'
    ];

    public function download()
    {
        return $this->belongsTo(FlickrDownload::class, 'download_id');
    }

    public function photo()
    {
        return $this->belongsTo(FlickrPhoto::class, 'photo_id');
    }
}
