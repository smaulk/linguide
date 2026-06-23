<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Mappers;

use App\Core\Modules\Term\Models\TermVariant;
use App\Core\Modules\User\Vo\UtcOffset;
use App\Core\Modules\Term\Dto\TranslationExampleDto;
use App\Core\Modules\Term\Dto\TermVariantDto;
use App\Core\Modules\Term\Dto\LearningProgressDto;
use App\Core\Modules\Term\Dto\TranslationDto;
use App\Core\Modules\Term\Models\TranslationExample;
use App\Core\Modules\Term\Models\LearningProgress;
use App\Core\Modules\Term\Models\Translation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

final class LearningProgressMapper
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
            easeFactor: $progress->ease_factor,
            dueAt: $this->applyTimezone($progress->due_at, $utcOffset),
            lastReviewedAt: $progress->last_reviewed_at !== null
                ? $this->applyTimezone($progress->last_reviewed_at, $utcOffset)
                : null,
            createdAt: $progress->created_at !== null
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
            contextEn: $translation->context_en,
            contextRu: $translation->context_ru,
            examples: $examples,
        );
    }

    public function mapExampleModelToDto(TranslationExample $example): TranslationExampleDto
    {
        return new TranslationExampleDto(
            id: $example->id,
            sentenceEn: $example->sentence_en,
            sentenceRu: $example->sentence_ru,
        );
    }
}