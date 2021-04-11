<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MovieAttribute extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'movie_id',
        'model_type',
        'model_id',
    ];

    public function model()
    {
        return $this->morphTo();
    }

    public function tag()
    {
        return $this->belongsTo(Tag::class);
    }
}
