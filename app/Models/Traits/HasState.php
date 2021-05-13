<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasState
{
    public function scopeByState(Builder $builder, string $state)
    {
        return $builder->where(['state_code' => $state]);
    }

    public function updateState(string $state): self
    {
        $this->state_code = $state;
        $this->save();

        return $this;
    }
}
