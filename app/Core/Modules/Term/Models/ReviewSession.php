<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Models;

use App\Core\Common\Parents\Model;
use App\Core\Modules\User\Models\User;
use App\Core\Modules\Term\Enums\ReviewSessionStatus;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property ReviewSessionStatus $status
 * @property Carbon $started_at
 * @property ?Carbon $finished_at
 * @property ?Carbon $updated_at
 *
 * @property-read User $user
 * @property-read Collection<int, ReviewSessionItem> $items
 */
final class ReviewSession extends Model
{
    const ?string CREATED_AT = null;

    public const int SESSION_TIMEOUT_MINUTES = 60;

    protected $table = 'review_sessions';

    protected $casts = [
        'status'      => ReviewSessionStatus::class,
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
     * @return HasMany<ReviewSessionItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(ReviewSessionItem::class, 'session_id', 'id');
    }
}