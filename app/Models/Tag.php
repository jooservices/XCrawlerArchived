<?php

namespace App\Models;

use App\Models\Traits\HasName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasName;

    protected $fillable = [
        'name',
    ];

    public function favorite()
    {
        return $this->morphOne(Favorite::class, 'model');
    }
}
