<?php
declare(strict_types=1);

namespace App\Core\Modules\Words\Models;

use App\Core\Common\Parents\Model;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $translation_id
 * @property string $sentence_en
 * @property string $sentence_ru
 * @property ?DateTimeInterface $created_at
 *
 * @property-read WordTranslation $translation
 */
final class TranslationExample extends Model
{
    /**
     * @return BelongsTo<WordTranslation, $this>
     */
    public function translation(): BelongsTo
    {
        return $this->belongsTo(WordTranslation::class, 'translation_id', 'id');
    }
}