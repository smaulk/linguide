<?php
declare(strict_types=1);

namespace App\Core\Modules\Words\Models;

use App\Core\Common\Parents\Model;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Collection;

/**
 * @property int $id
 * @property int $word_id
 * @property string $text
 * @property int $rank
 * @property string $context_en
 * @property string $context_ru
 * @property ?DateTimeInterface $created_at
 *
 * @property-read Word $word
 * @property-read Collection<int, TranslationExample> $examples
 */
final class WordTranslation extends Model
{
    /**
     * @return BelongsTo<Word, $this>
     */
    public function word(): BelongsTo
    {
        return $this->belongsTo(Word::class, 'word_id', 'id');
    }

    /**
     * @return HasMany<TranslationExample, $this>
     */
    public function examples(): HasMany
    {
        return $this->hasMany(TranslationExample::class, 'translation_id', 'id');
    }
}