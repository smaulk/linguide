<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Models;

use App\Core\Common\Parents\Model;
use App\Core\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property int $candidate_id
 * @property boolean $is_processed
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property-read User $user
 * @property-read TermCandidate $candidate
 */
final class UserTermCandidate extends Model
{
    protected $table = 'user_term_candidates';

    protected $casts = [
        'is_processed' => 'boolean',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * @return BelongsTo<TermCandidate, $this>
     */
    public function candidate(): BelongsTo
    {
        return $this->belongsTo(TermCandidate::class, 'candidate_id', 'id');
    }
}