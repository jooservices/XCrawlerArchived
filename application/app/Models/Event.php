<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

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
    ];

    /**
     * A Event belongs to a Model
     *
     * @return MorphTo
     */
    public function model()
    {
        return $this->morphTo();
    }
}
