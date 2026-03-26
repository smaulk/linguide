<?php
declare(strict_types=1);

namespace App\Core\Modules\Words\Mappers;

use App\Core\Modules\User\Vo\UtcOffset;
use App\Core\Modules\Words\Dto\TranslationExampleDto;
use App\Core\Modules\Words\Dto\WordDto;
use App\Core\Modules\Words\Dto\WordProgressDto;
use App\Core\Modules\Words\Dto\WordTranslationDto;
use App\Core\Modules\Words\Models\TranslationExample;
use App\Core\Modules\Words\Models\UserWordProgress;
use App\Core\Modules\Words\Models\Word;
use App\Core\Modules\Words\Models\WordTranslation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

final class WordMapper
{
    /**
     * @param Collection<int, UserWordProgress> $wordsProgress
     * @return Collection<int, WordProgressDto>
     */
    public function mapCollectionWordProgressModelToDto(
        Collection $wordsProgress, ?UtcOffset $utcOffset = null,
    ): Collection
    {
        return $wordsProgress->map(
            fn(UserWordProgress $progress) => $this->mapWordProgressModelToDto($progress, $utcOffset)
        );
    }

    public function mapWordProgressModelToDto(UserWordProgress $progress, ?UtcOffset $utcOffset): WordProgressDto
    {
        return new WordProgressDto(
            id: $progress->id,
            repetitions: $progress->repetitions,
            interval: $progress->interval,
            ease_factor: $progress->ease_factor,
            due_at: $this->applyTimezone($progress->due_at, $utcOffset),
            last_reviewed_at: $progress->last_reviewed_at !== null
                ? $this->applyTimezone($progress->last_reviewed_at, $utcOffset)
                : null,
            created_at: $progress->created_at !== null
                ? $this->applyTimezone($progress->created_at, $utcOffset)
                : null,
            word: $this->mapWordModelToDto($progress->word)
        );
    }

    private function applyTimezone(Carbon $date, ?UtcOffset $utcOffset): Carbon
    {
        if ($utcOffset === null) {
            return $date;
        }

        return $utcOffset->applyTo($date);
    }

    /**
     * @param Collection<int, Word> $words
     * @return Collection<int, WordDto>
     */
    public function mapCollectionWordModelToDto(Collection $words): Collection
    {
        return $words->map(fn(Word $word) => $this->mapWordModelToDto($word));
    }

    public function mapWordModelToDto(Word $word): WordDto
    {
        $translations = [];
        if ($word->relationLoaded('translations') && $word->translations->isNotEmpty()) {
            $translations = $word->translations
                ->map(fn(WordTranslation $t) => $this->mapTranslationModelToDto($t))
                ->all();
        }

        return new WordDto(
            id: $word->id,
            text: $word->text,
            pos: $word->pos,
            level: $word->level,
            translations: $translations,
        );
    }

    public function mapTranslationModelToDto(WordTranslation $translation): WordTranslationDto
    {
        $examples = [];
        if ($translation->relationLoaded('examples') && $translation->examples->isNotEmpty()) {
            $examples = $translation->examples
                ->map(fn(TranslationExample $e) => $this->mapExampleModelToDto($e))
                ->all();
        }

        return new WordTranslationDto(
            id: $translation->id,
            text: $translation->text,
            rank: $translation->rank,
            context_en: $translation->context_en,
            context_ru: $translation->context_ru,
            examples: $examples,
        );
    }

    public function mapExampleModelToDto(TranslationExample $example): TranslationExampleDto
    {
        return new TranslationExampleDto(
            id: $example->id,
            sentence_en: $example->sentence_en,
            sentence_ru: $example->sentence_ru,
        );
    }
}