<?php

namespace App\Models;

use App\Models\Traits\HasMovieObserver;
use App\Models\Interfaces\MovieInterface;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractJavMovie extends Model implements MovieInterface
{
    use HasMovieObserver;

    public function isDownloadable(): bool
    {
        return false;
    }
}
