<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Models;

use App\Core\Common\Parents\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $session_id
 * @property int $variant_id
 * @property bool $is_correct
 * @property ?Carbon $presented_at
 * @property ?Carbon $answered_at
 *
 * @property-read ReviewSession $session
 * @property-read TermVariant $variant
 */
final class ReviewSessionItem extends Model
{
    protected $table = 'review_session_items';
    public $timestamps = false;

    protected $casts = [
        'is_correct'   => 'boolean',
        'presented_at' => 'datetime',
        'answered_at'  => 'datetime',
    ];

    /**
     * @return BelongsTo<ReviewSession, $this>
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(ReviewSession::class, 'session_id', 'id');
    }

    /**
     * @return BelongsTo<TermVariant, $this>
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(TermVariant::class, 'variant_id', 'id');
    }
}