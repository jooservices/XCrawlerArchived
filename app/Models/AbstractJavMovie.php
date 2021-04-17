<?php

namespace App\Models;

use App\Models\Interfaces\MovieInterface;
use App\Models\Traits\HasEvents;
use App\Models\Traits\HasMovieObserver;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractJavMovie extends Model implements MovieInterface
{
    use HasMovieObserver;
    use HasEvents;

    public function getName(): ?string
    {
        return null;
    }

    public function isDownloadable(): bool
    {
        return false;
    }
}
