<?php
declare(strict_types=1);

namespace App\Core\Modules\Dictionary\Mappers;

use App\Core\Modules\Dictionary\Models\TermVariant;
use App\Core\Modules\User\Vo\UtcOffset;
use App\Core\Modules\Dictionary\Dto\TranslationExampleDto;
use App\Core\Modules\Dictionary\Dto\TermVariantDto;
use App\Core\Modules\Dictionary\Dto\LearningProgressDto;
use App\Core\Modules\Dictionary\Dto\TranslationDto;
use App\Core\Modules\Dictionary\Models\TranslationExample;
use App\Core\Modules\Dictionary\Models\LearningProgress;
use App\Core\Modules\Dictionary\Models\Term;
use App\Core\Modules\Dictionary\Models\Translation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

final class TermMapper
{
    /**
     * @param Collection<int, LearningProgress> $learningProgress
     * @return Collection<int, LearningProgressDto>
     */
    public function mapCollectionLearningProgressModelToDto(
        Collection $learningProgress, ?UtcOffset $utcOffset = null,
    ): Collection
    {
        return $learningProgress->map(
            fn(LearningProgress $progress) => $this->mapLearningProgressModelToDto($progress, $utcOffset)
        );
    }

    public function mapLearningProgressModelToDto(LearningProgress $progress, ?UtcOffset $utcOffset = null): LearningProgressDto
    {
        return new LearningProgressDto(
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
            termVariant: $this->mapTermVariantModelToDto($progress->variant)
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
     * @param Collection<int, TermVariant> $variants
     * @return Collection<int, TermVariantDto>
     */
    public function mapCollectionTermVariantModelToDto(Collection $variants): Collection
    {
        return $variants->map(fn(TermVariant $variant) => $this->mapTermVariantModelToDto($variant));
    }

    public function mapTermVariantModelToDto(TermVariant $variant): TermVariantDto
    {
        $translations = [];
        if ($variant->relationLoaded('translations') && $variant->translations->isNotEmpty()) {
            $translations = $variant->translations
                ->map(fn(Translation $t) => $this->mapTranslationModelToDto($t))
                ->all();
        }

        return new TermVariantDto(
            id: $variant->id,
            text: $variant->term->text,
            type: $variant->term->type,
            pos: $variant->pos,
            level: $variant->level,
            translations: $translations,
        );
    }

    public function mapTranslationModelToDto(Translation $translation): TranslationDto
    {
        $examples = [];
        if ($translation->relationLoaded('examples') && $translation->examples->isNotEmpty()) {
            $examples = $translation->examples
                ->map(fn(TranslationExample $e) => $this->mapExampleModelToDto($e))
                ->all();
        }

        return new TranslationDto(
            id: $translation->id,
            text: $translation->text,
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