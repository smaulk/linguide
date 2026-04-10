<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Models;

use App\Core\Common\Parents\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $translation_id
 * @property string $sentence_en
 * @property string $sentence_ru
 * @property ?Carbon $created_at
 *
 * @property-read Translation $translation
 */
final class TranslationExample extends Model
{
    const ?string UPDATED_AT = null;

    protected $table = 'translation_examples';

    /**
     * @return BelongsTo<Translation, $this>
     */
    public function translation(): BelongsTo
    {
        return $this->belongsTo(Translation::class, 'translation_id', 'id');
    }
}