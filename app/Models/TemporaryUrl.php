<?php

namespace App\Models;

use App\Models\Traits\HasSource;
use App\Models\Traits\HasState;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $url
 * @property string $source
 * @property array $data
 * @property string $state_code
 * @package App\Models
 */
class TemporaryUrl extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasState;
    use HasSource;

    public const STATE_INIT = 'TUIN';
    public const STATE_COMPLETED = 'TUCE';
    public const STATE_FAILED = 'TUFL';

    protected $fillable = [
        'url',
        'source',
        'data',
        'state_code'
    ];

    protected $casts = [
        'url' => 'string',
        'source' => 'string',
        'data' => 'array',
        'state_code' => 'string'
    ];

    public function completed()
    {
        $this->update(['state_code' => self::STATE_COMPLETED]);
    }

    public function updateData(array $data)
    {
        $this->update(['data' => array_merge($this->data, $data)]);
    }
}
