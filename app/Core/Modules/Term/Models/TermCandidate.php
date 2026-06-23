<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Models;

use App\Core\Common\Parents\Model;
use App\Core\Modules\Term\Enums\TermCandidateStatus;
use App\Core\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $raw_term
 * @property TermCandidateStatus $status
 * @property ?int $term_id
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property-read ?Term $term
 * @property-read Collection<int, User> $users
 */
final class TermCandidate extends Model
{
    protected $table = 'term_candidates';

    protected $casts = [
        'status' => TermCandidateStatus::class,
    ];

    /**
     * @return BelongsTo<Term, $this>
     */
    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class, 'term_id', 'id');
    }

    /**
     * @return BelongsToMany<User, $this>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_term_candidates', 'candidate_id', 'user_id');
    }
}