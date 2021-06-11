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
    public const STATE_TO_WORDPRESS = 'FDTW';
    public const STATE_COMPLETED = 'FDCE';
    public const STATE_FAILED = 'FDFL';

    protected $fillable = [
        'name',
        'path',
        'total',
        'model_id',
        'model_type',
        'state_code'
    ];

    protected $casts = [
        'name' => 'string',
        'path' => 'string',
        'total' => 'integer',
        'model_id' => 'string',
        'model_type' => 'string',
        'state_code' => 'string',
    ];

    public function model()
    {
        return $this->morphTo();
    }

    public function items()
    {
        return $this->hasMany(FlickrDownloadItem::class, 'download_id');
    }

    public function isCompleted(): bool
    {
        return $this->total === $this->items()->where(['state_code' => FlickrDownloadItem::STATE_COMPLETED])->count();
    }
}
