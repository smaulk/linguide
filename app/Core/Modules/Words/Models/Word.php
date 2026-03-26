<?php
declare(strict_types=1);

namespace App\Core\Modules\Words\Models;

use App\Core\Common\Parents\Model;
use App\Core\Modules\Words\Enums\PartOfSpeech;
use App\Core\Modules\User\Enums\LanguageLevel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $text
 * @property PartOfSpeech $pos
 * @property LanguageLevel $level
 * @property ?Carbon $created_at
 *
 * @property-read Collection<int, WordTranslation> $translations
 * @property-read Collection<int, UserWordProgress> $userProgress
 */
final class Word extends Model
{
    protected $casts = [
        'pos'   => PartOfSpeech::class,
        'level' => LanguageLevel::class,
    ];

    /**
     * @return HasMany<WordTranslation, $this>
     */
    public function translations(): HasMany
    {
        return $this->hasMany(WordTranslation::class, 'word_id', 'id');
    }

    /**
     * @return HasMany<UserWordProgress, $this>
     */
    public function userProgress(): HasMany
    {
        return $this->hasMany(UserWordProgress::class, 'word_id', 'id');
    }
}