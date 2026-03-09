<?php
declare(strict_types=1);

namespace App\Core\Modules\Words\Models;

use App\Core\Common\Parents\Model;
use App\Core\Modules\Words\Enums\PartOfSpeechType;
use App\Core\Modules\User\Enums\LanguageLevel;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $text
 * @property PartOfSpeechType $pos
 * @property LanguageLevel $level
 * @property ?DateTimeInterface $created_at
 *
 * @property-read Collection<int, WordTranslation> $translations
 */
final class Word extends Model
{
    protected $casts = [
        'pos' => PartOfSpeechType::class,
        'level' => LanguageLevel::class,
    ];

    /**
     * @return HasMany<WordTranslation, $this>
     */
    public function translations(): HasMany
    {
        return $this->hasMany(WordTranslation::class, 'word_id', 'id');
    }
}