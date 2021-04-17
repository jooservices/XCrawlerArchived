<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Favorite extends Model
{
    use HasFactory;

    /**
     * @return MorphTo
     */
    public function model()
    {
        return $this->morphTo();
    }

    public function scopeFilterType(Builder $builder, string $type)
    {
        return $builder->where(['model_type' => $type]);
    }
}
