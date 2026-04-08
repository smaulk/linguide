<?php
declare(strict_types=1);

namespace App\Core\Modules\Dictionary\Models;

use App\Core\Common\Parents\Model;
use App\Core\Modules\Dictionary\Enums\PartOfSpeech;
use App\Core\Modules\User\Enums\LanguageLevel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Collection;

/**
 * @property int $id
 * @property int $term_id
 * @property PartOfSpeech $pos
 * @property ?LanguageLevel $level
 * @property ?Carbon $created_at
 *
 * @property-read Term $term
 * @property-read Collection<int, Translation> $translations
 * @property-read Collection<int, LearningProgress> $progress
 * @property-read Collection<int, ReviewSessionItem> $sessionItems
 */
final class TermVariant extends Model
{
    const ?string UPDATED_AT = null;

    protected $table = 'term_variants';

    protected $casts = [
        'pos'   => PartOfSpeech::class,
        'level' => LanguageLevel::class,
    ];

    /**
     * @return BelongsTo<Term, $this>
     */
    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class, 'term_id', 'id');
    }

    /**
     * @return HasMany<Translation, $this>
     */
    public function translations(): HasMany
    {
        return $this->hasMany(Translation::class, 'variant_id', 'id');
    }

    /**
     * @return HasMany<LearningProgress, $this>
     */
    public function progress(): HasMany
    {
        return $this->hasMany(LearningProgress::class, 'variant_id', 'id');
    }

    /**
     * @return HasMany<ReviewSessionItem, $this>
     */
    public function sessionItems(): HasMany
    {
        return $this->hasMany(ReviewSessionItem::class, 'variant_id', 'id');
    }
}