<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property array  $sizes
 */
class FlickrPhoto extends Model
{
    use HasFactory;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'id',
        'owner',
        'secret',
        'server',
        'farm',
        'title',
        'ispublic',
        'isfriend',
        'isfamily',
        'sizes',
        'isprimary',
    ];

    protected $casts = [
        'owner' => 'string',
        'secret' => 'string',
        'server' => 'string',
        'farm' => 'string',
        'title' => 'string',
        'ispublic' => 'integer',
        'isfriend' => 'integer',
        'isfamily' => 'integer',
        'isprimary' => 'integer',
        'sizes' => 'array',
    ];

    public function hasSizes(): bool
    {
        return null !== $this->sizes;
    }

    public function getLargestSize(): array
    {
        $sizes = $this->sizes['size'];

        return end($sizes);
    }

    public function downloadItem()
    {
        return $this->belongsTo(DownloadItem::class, 'id', 'model_id')
            ->where('model_type', FlickrPhoto::class);
    }
}
