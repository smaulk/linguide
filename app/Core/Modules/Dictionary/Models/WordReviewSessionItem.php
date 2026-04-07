<?php
declare(strict_types=1);

namespace App\Core\Modules\Dictionary\Models;

use App\Core\Common\Parents\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $session_id
 * @property int $word_id
 * @property bool $is_correct
 * @property ?Carbon $presented_at
 * @property ?Carbon $answered_at
 *
 * @property-read WordReviewSession $session
 * @property-read Word $word
 */
final class WordReviewSessionItem extends Model
{
    public $timestamps = false;

    protected $casts = [
        'is_correct'   => 'boolean',
        'presented_at' => 'datetime',
        'answered_at'  => 'datetime',
    ];

    /**
     * @return BelongsTo<WordReviewSession, $this>
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(WordReviewSession::class, 'session_id', 'id');
    }

    /**
     * @return BelongsTo<Word, $this>
     */
    public function word(): BelongsTo
    {
        return $this->belongsTo(Word::class, 'word_id', 'id');
    }
}