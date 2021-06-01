<?php

namespace App\Models\Traits;

use App\Models\State;
use Illuminate\Database\Eloquent\Builder;

/**
 * Trait HasStates
 * @property string $state_code
 * @package App\Models\Traits
 */
trait HasStates
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

    public function states()
    {
        return State::where(['entity' => get_class($this)])->get();
    }
}
