<?php
declare(strict_types=1);

namespace App\Core\Modules\Dictionary\Models;

use App\Core\Common\Parents\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $variant_id
 * @property string $text
 * @property string $context_en
 * @property string $context_ru
 * @property ?Carbon $created_at
 *
 * @property-read TermVariant $variant
 * @property-read Collection<int, TranslationExample> $examples
 */
final class Translation extends Model
{
    const ?string UPDATED_AT = null;

    protected $table = 'translations';

    /**
     * @return BelongsTo<TermVariant, $this>
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(TermVariant::class, 'variant_id', 'id');
    }

    /**
     * @return HasMany<TranslationExample, $this>
     */
    public function examples(): HasMany
    {
        return $this->hasMany(TranslationExample::class, 'translation_id', 'id');
    }
}