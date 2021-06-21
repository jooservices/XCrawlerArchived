<?php

namespace App\Models;

use App\Models\Traits\HasSource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

/**
 * @property boolean $succeed
 * @method static Builder|XCrawlerLog bySource (string $source)
 * @package App\Models
 */
class XCrawlerLog extends Model
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    use HasSource;

    protected $fillable = [
        'url',
        'payload',
        'response',
        'source',
        'succeed'
    ];

    protected $casts = [
        'url' => 'string',
        'payload' => 'array',
        'response' => 'string',
        'source' => 'string',
        'succeed' => 'boolean'
    ];
}
