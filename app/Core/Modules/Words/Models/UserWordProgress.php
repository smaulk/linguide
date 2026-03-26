<?php
declare(strict_types=1);

namespace App\Core\Modules\Words\Models;

use App\Core\Common\Parents\Model;
use App\Core\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $userId
 * @property int $wordId
 * @property int $repetitions
 * @property int $interval
 * @property float $ease_factor
 * @property Carbon $due_at
 * @property ?Carbon $last_reviewed_at
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property-read User $user
 * @property-read Word $word
 */
final class UserWordProgress extends Model
{
    public const int FIRST_INTERVAL  = 1;
    public const int SECOND_INTERVAL = 3;
    public const int MAX_INTERVAL    = 365;

    public const float MAX_EASE_FACTOR = 2.8;
    public const float MIN_EASE_FACTOR = 1.3;

    public const float CORRECT_EASE_STEP = 0.05;
    public const float WRONG_EASE_STEP   = 0.2;

    public const float DEFAULT_EASE_FACTOR = 2.5;


    protected $table = 'user_word_progress';

    protected $casts = [
        'ease_factor'      => 'float',
        'due_at'           => 'datetime',
        'last_reviewed_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * @return BelongsTo<Word, $this>
     */
    public function word(): BelongsTo
    {
        return $this->belongsTo(Word::class, 'word_id', 'id');
    }
}