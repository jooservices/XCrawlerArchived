<?php

namespace App\Models;

use App\Models\Traits\HasStates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $name
 * @property string $path
 * @property integer $total
 * @package App\Models
 */
class FlickrDownload extends Model
{
    use HasFactory;
    use HasStates;

    public const STATE_INIT = 'FDIN';
    public const STATE_COMPLETED = 'FDCE';

    protected $fillable = [
        'name',
        'path',
        'total',
        'state_code'
    ];

    protected $casts = [
        'name' => 'string',
        'path' => 'string',
        'total' => 'integer',
        'state_code' => 'string',
    ];

    public function items()
    {
        return $this->hasMany(FlickrDownloadItem::class, 'download_id');
    }

    public function isCompleted(): bool
    {
        return $this->total === $this->items()->where(['state_code' => FlickrDownloadItem::STATE_COMPLETED])->count();
    }
}
