<?php
declare(strict_types=1);

namespace App\Core\Modules\Dictionary\Models;

use App\Core\Common\Parents\Model;
use App\Core\Modules\User\Models\User;
use App\Core\Modules\Dictionary\Enums\WordReviewSessionStatus;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property WordReviewSessionStatus $status
 * @property Carbon $started_at
 * @property ?Carbon $finished_at
 * @property ?Carbon $updated_at
 *
 * @property-read User $user
 * @property-read Collection<int, WordReviewSessionItem> $items
 */
final class WordReviewSession extends Model
{
    const ?string CREATED_AT = null;

    public const int SESSION_TIMEOUT_MINUTES = 60;

    protected $casts = [
        'status'      => WordReviewSessionStatus::class,
        'started_at'  => 'datetime',
        'finished_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * @return HasMany<WordReviewSessionItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(WordReviewSessionItem::class, 'session_id', 'id');
    }
}