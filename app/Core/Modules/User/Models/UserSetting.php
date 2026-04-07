<?php
declare(strict_types=1);

namespace App\Core\Modules\User\Models;

use App\Core\Common\Parents\Model;
use App\Core\Modules\User\Enums\LanguageLevel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $user_id
 * @property ?LanguageLevel $level
 * @property ?int $utc_offset
 * @property int $words_review_limit
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property-read User $user
 */
final class UserSetting extends Model
{
    public const int WORD_REPEAT_LIMIT_DEFAULT = 7;

    protected $primaryKey = 'user_id';
    public $incrementing = false;

    protected $casts = [
        'level' => LanguageLevel::class,
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}