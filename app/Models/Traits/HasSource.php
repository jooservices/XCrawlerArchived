<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * @property string $source
 * @package App\Models\Traits
 */
trait HasSource
{
    public function scopeBySource(Builder $builder, string $source)
    {
        return $builder->where(['source' => $source]);
    }
}
