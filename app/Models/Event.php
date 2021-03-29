<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * App/Models/Event
 *
 * @property int $model_id
 * @property string $model_type
 * @property string $category
 * @property string $event
 * @property array $data
 * @property bool $is_reverted
 * @property int $ip_address
 */
class Event extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'model_id',
        'model_type',
        'category',
        'event',
        'data',
        'is_reverted',
        'ip_address',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'model_type' => 'string',
        'model_id' => 'integer',
        'category' => 'string',
        'event' => 'string',
        'data' => 'array',
        'is_reverted' => 'boolean',
        'ip_address' => 'integer',
    ];

    /**
     * A Event belongs to a Model
     *
     * @return MorphTo
     */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    public function getRawIpAddress(): string
    {
        return $this->ip_address ? long2ip($this->ip_address) : '';
    }
}
