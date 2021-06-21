<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $service
 * @property string $token
 * @property string|null $token_secret
 * @property string|null $expires_in
 * @package App\Models
 */
class Integration extends Model
{
    use HasFactory;

    protected $fillable = [
        'service',
        'token',
        'token_secret',
        'expires_in',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function scopeForService(Builder $builder, string $service)
    {
        return $builder->where(['service' => $service]);
    }
}
