<?php

namespace App\Models;

use App\Models\Interfaces\MovieInterface;
use App\Models\Traits\HasEvents;
use App\Models\Traits\HasMovieObserve;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $dvd_id
 * @property array $tags
 * @property array $actresses
 * @package App\Models
 */
abstract class AbstractJavMovie extends Model implements MovieInterface
{
    use HasMovieObserve;
    use HasEvents;

    public function getDvdId(): ?string
    {
        return $this->dvd_id ?? null;
    }

    public function getName(): ?string
    {
        return $this->name ?? null;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function getActresses(): array
    {
        return $this->actresses;
    }

    public function isDownloadable(): bool
    {
        return false;
    }
}
