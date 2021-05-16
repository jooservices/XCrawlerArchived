<?php

namespace App\Models;

use App\Models\Traits\HasStates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $nsid
 * @package App\Models
 */
class FlickrContact extends Model
{
    use HasFactory;
    use HasStates;

    public const STATE_INIT = 'FCIN';
    public const STATE_INFO_COMPLETED = 'FCIC';
    public const STATE_INFO_FAILED = 'FCIF';
    public const STATE_PHOTOS_PROCESSING = 'FCPP';
    public const STATE_PHOTOS_COMPLETED = 'FCPC';
    public const STATE_PHOTOS_FAILED = 'FCPF';
    public const STATE_ALBUM_PROCESSING = 'FCAP';
    public const STATE_ALBUM_COMPLETED = 'FCAC';
    public const STATE_ALBUM_FAILED = 'FCAF';

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

    /**
     * @param string $nsid
     * @return self
     */
    public static function findByNsid(string $nsid): self
    {
        return self::where('nsid', $nsid)->first();
    }
}
