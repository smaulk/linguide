<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Mappers;

use App\Core\Modules\Term\Dto\StoreTranslationDto;
use App\Core\Modules\Term\Dto\StoreTranslationExampleDto;
use App\Core\Modules\Term\Dto\StoreTranslationTermDto;
use App\Core\Modules\Term\Enums\PartOfSpeech;
use App\Core\Modules\Term\Enums\TermType;
use App\Core\Modules\Term\Models\TermVariant;
use App\Core\Modules\Term\Models\TranslationExample;
use App\Core\Modules\Term\Models\Translation;
use Illuminate\Database\Eloquent\Collection;

final class StoreTranslationMapper
{
    //RAW -> DTO

    /**
     * @param array<int, array<string, mixed>> $rawTerms
     * @return StoreTranslationTermDto[]
     */
    public function mapRawArrayToDtoArray(array $rawTerms): array
    {
        $data = [];
        foreach ($rawTerms as $rawTerm) {
            $data[] = $this->mapRawToDto($rawTerm);
        }

        return $data;
    }

    /**
     * @param array<string, mixed> $rawTerm
     * @return StoreTranslationTermDto
     */
    public function mapRawToDto(array $rawTerm): StoreTranslationTermDto
    {
        $posRaw = $rawTerm['pos'] ?? null;
        $pos = $posRaw !== null
            ? PartOfSpeech::from(strtolower(trim($posRaw)))
            : PartOfSpeech::UNKNOWN;

        return new StoreTranslationTermDto(
            text: strtolower(trim($rawTerm['term'])),
            type: TermType::from(strtolower(trim($rawTerm['type']))),
            pos: $pos,
            translations: $this->mapRawTranslationsToDto($rawTerm['translations'] ?? []),
        );
    }

    /**
     * @param array<int, array<string, mixed>> $rawTranslations
     * @return StoreTranslationDto[]
     */
    private function mapRawTranslationsToDto(array $rawTranslations): array
    {
        return array_map(fn(array $translation) => new StoreTranslationDto(
            text: trim($translation['translation']),
            contextEn: trim($translation['context_en']),
            contextRu: trim($translation['context_ru']),
            examples: $this->mapRawExamplesToDto($translation['examples'] ?? [])
        ), $rawTranslations);
    }

    /**
     * @param array<int, array<string, string>> $rawExamples
     * @return StoreTranslationExampleDto[]
     */
    private function mapRawExamplesToDto(array $rawExamples): array
    {
        return array_map(fn(array $example) => new StoreTranslationExampleDto(
            sentenceEn: trim($example['sentence_en']),
            sentenceRu: trim($example['sentence_ru']),
        ), $rawExamples);
    }



    // DTO -> RAW

    /**
     * @param StoreTranslationTermDto[] $dtoTerms
     * @return array<int, array<string, mixed>>
     */
    public function mapDtoArrayToRawArray(array $dtoTerms): array
    {
        $data = [];
        foreach ($dtoTerms as $dtoTerm) {
            $data[] = $this->mapDtoToRaw($dtoTerm);
        }

        return $data;
    }

    /**
     * @param StoreTranslationTermDto $dtoTerm
     * @return array<string, mixed>
     */
    public function mapDtoToRaw(StoreTranslationTermDto $dtoTerm): array
    {
        $raw = [
            'term' => $dtoTerm->text,
            'type' => $dtoTerm->type->value,
        ];

        if ($dtoTerm->type === TermType::WORD) {
            $raw['pos'] = $dtoTerm->pos->value;
        }

        $raw['translations'] = $this->mapDtoTranslationsToRaw($dtoTerm->translations);

        return $raw;
    }

    /**
     * @param StoreTranslationDto[] $dtoTranslations
     * @return array<int, array<string, mixed>>
     */
    private function mapDtoTranslationsToRaw(array $dtoTranslations): array
    {
        return array_map(fn(StoreTranslationDto $translation) => [
            'translation' => $translation->text,
            'context_en'  => $translation->contextEn,
            'context_ru'  => $translation->contextRu,
            'examples'    => $this->mapDtoExamplesToRaw($translation->examples),
        ], $dtoTranslations);
    }

    /**
     * @param StoreTranslationExampleDto[] $dtoExamples
     * @return array<int, array<string, string>>
     */
    private function mapDtoExamplesToRaw(array $dtoExamples): array
    {
        return array_map(fn(StoreTranslationExampleDto $example) => [
            'sentence_en' => $example->sentenceEn,
            'sentence_ru' => $example->sentenceRu,
        ], $dtoExamples);
    }



    // MODEL -> DTO

    /**
     * @param TermVariant[] $modelVariants
     * @return StoreTranslationTermDto[]
     */
    public function mapModelArrayToDtoArray(array $modelVariants): array
    {
        $data = [];
        foreach ($modelVariants as $modelVariant) {
            $data[] = $this->mapModelToDto($modelVariant);
        }

        return $data;
    }

    /**
     * @param TermVariant $modelVariant
     * @return StoreTranslationTermDto
     */
    public function mapModelToDto(TermVariant $modelVariant): StoreTranslationTermDto
    {
        return new StoreTranslationTermDto(
            text: $modelVariant->term->text,
            type: $modelVariant->term->type,
            pos: $modelVariant->pos,
            translations: $this->mapModelTranslationsToDto($modelVariant->translations),
        );
    }

    /**
     * @param Collection<int, Translation> $translations
     * @return StoreTranslationDto[]
     */
    private function mapModelTranslationsToDto(Collection $translations): array
    {
        return $translations->map(fn(Translation $translation) => new StoreTranslationDto(
            text: $translation->text,
            contextEn: $translation->context_en,
            contextRu: $translation->context_ru,
            examples: $this->mapModelExamplesToDto($translation->examples)
        ))->all();
    }

    /**
     * @param Collection<int, TranslationExample> $examples
     * @return StoreTranslationExampleDto[]
     */
    private function mapModelExamplesToDto(Collection $examples): array
    {
        return $examples->map(fn(TranslationExample $example) => new StoreTranslationExampleDto(
            sentenceEn: $example->sentence_en,
            sentenceRu: $example->sentence_ru,
        ))->all();
    }
}