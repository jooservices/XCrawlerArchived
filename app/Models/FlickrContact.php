<?php

namespace App\Models;

use App\Models\Traits\HasState;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $nsid
 * @package App\Models
 */
class FlickrContact extends Model
{
    use HasFactory;
    use HasState;

    public const STATE_INIT = 'FCIN';
    public const STATE_PEOPLE_INFO = 'FCPF';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'nsid';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'nsid',
        'ispro',
        'pro_badge',
        'expire',
        'can_buy_pro',
        'iconserver',
        'iconfarm',
        'ignored',
        'path_alias',
        'has_stats',
        'gender',
        'contact',
        'friend',
        'family',
        'revcontact',
        'revfriend',
        'revfamily',
        'rev_ignored',
        'username',
        'realname',
        'mbox_sha1sum',
        'location',
        'timezone',
        'description',
        'photosurl',
        'profileurl',
        'mobileurl',
        'photos',
        'photos_count',
        'state_code',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'nsid' => 'string',
        'ispro' => 'int',
        'pro_badge' => 'string',
        'expire' => 'int',
        'can_buy_pro' => 'int',
        'iconserver' => 'string',
        'iconfarm' => 'string',
        'ignored' => 'int',
        'path_alias' => 'string',
        'has_stats' => 'int',
        'gender' => 'string',
        'contact' => 'int',
        'friend' => 'int',
        'family' => 'int',
        'revcontact' => 'int',
        'revfriend' => 'int',
        'revfamily' => 'int',
        'rev_ignored' => 'int',
        'username' => 'string',
        'realname' => 'string',
        'mbox_sha1sum' => 'string',
        'location' => 'string',
        'timezone' => 'array',
        'description' => 'string',
        'photosurl' => 'string',
        'profileurl' => 'string',
        'mobileurl' => 'string',
        'photos' => 'array',
        'photos_count' => 'integer',
        'state_code' => 'string',
    ];
}
