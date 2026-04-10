<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Mappers;

use App\Core\Modules\Term\Dto\TranslationDatasetDto;
use App\Core\Modules\Term\Dto\TranslationExampleDatasetDto;
use App\Core\Modules\Term\Dto\TermTranslationDatasetDto;
use App\Core\Modules\Term\Enums\PartOfSpeech;
use App\Core\Modules\Term\Enums\TermType;
use App\Core\Modules\Term\Models\TermVariant;
use App\Core\Modules\Term\Models\TranslationExample;
use App\Core\Modules\Term\Models\Translation;
use Illuminate\Database\Eloquent\Collection;

final class TranslationsDatasetMapper
{
    //RAW -> DTO

    /**
     * @param array<int, array<string, mixed>> $rawTerms
     * @return TermTranslationDatasetDto[]
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
     * @return TermTranslationDatasetDto
     */
    public function mapRawToDto(array $rawTerm): TermTranslationDatasetDto
    {
        $posRaw = $rawTerm['pos'] ?? null;
        $pos = $posRaw !== null
            ? PartOfSpeech::from(strtolower(trim($posRaw)))
            : PartOfSpeech::UNKNOWN;

        return new TermTranslationDatasetDto(
            text: strtolower(trim($rawTerm['term'])),
            type: TermType::from(strtolower(trim($rawTerm['type']))),
            pos: $pos,
            translations: $this->mapRawTranslationsToDto($rawTerm['translations'] ?? []),
        );
    }

    /**
     * @param array<int, array<string, mixed>> $rawTranslations
     * @return TranslationDatasetDto[]
     */
    private function mapRawTranslationsToDto(array $rawTranslations): array
    {
        return array_map(fn(array $translation) => new TranslationDatasetDto(
            text: trim($translation['translation']),
            context_en: trim($translation['context_en']),
            context_ru: trim($translation['context_ru']),
            examples: $this->mapRawExamplesToDto($translation['examples'] ?? [])
        ), $rawTranslations);
    }

    /**
     * @param array<int, array<string, string>> $rawExamples
     * @return TranslationExampleDatasetDto[]
     */
    private function mapRawExamplesToDto(array $rawExamples): array
    {
        return array_map(fn(array $example) => new TranslationExampleDatasetDto(
            sentence_en: trim($example['sentence_en']),
            sentence_ru: trim($example['sentence_ru']),
        ), $rawExamples);
    }



    // DTO -> RAW

    /**
     * @param TermTranslationDatasetDto[] $dtoTerms
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
     * @param TermTranslationDatasetDto $dtoTerm
     * @return array<string, mixed>
     */
    public function mapDtoToRaw(TermTranslationDatasetDto $dtoTerm): array
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
     * @param TranslationDatasetDto[] $dtoTranslations
     * @return array<int, array<string, mixed>>
     */
    private function mapDtoTranslationsToRaw(array $dtoTranslations): array
    {
        return array_map(fn(TranslationDatasetDto $translation) => [
            'translation' => $translation->text,
            'context_en'  => $translation->context_en,
            'context_ru'  => $translation->context_ru,
            'examples'    => $this->mapDtoExamplesToRaw($translation->examples),
        ], $dtoTranslations);
    }

    /**
     * @param TranslationExampleDatasetDto[] $dtoExamples
     * @return array<int, array<string, string>>
     */
    private function mapDtoExamplesToRaw(array $dtoExamples): array
    {
        return array_map(fn(TranslationExampleDatasetDto $example) => [
            'sentence_en' => $example->sentence_en,
            'sentence_ru' => $example->sentence_ru,
        ], $dtoExamples);
    }



    // MODEL -> DTO

    /**
     * @param TermVariant[] $modelVariants
     * @return TermTranslationDatasetDto[]
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
     * @return TermTranslationDatasetDto
     */
    public function mapModelToDto(TermVariant $modelVariant): TermTranslationDatasetDto
    {
        return new TermTranslationDatasetDto(
            text: $modelVariant->term->text,
            type: $modelVariant->term->type,
            pos: $modelVariant->pos,
            translations: $this->mapModelTranslationsToDto($modelVariant->translations),
        );
    }

    /**
     * @param Collection<int, Translation> $translations
     * @return TranslationDatasetDto[]
     */
    private function mapModelTranslationsToDto(Collection $translations): array
    {
        return $translations->map(fn(Translation $translation) => new TranslationDatasetDto(
            text: $translation->text,
            context_en: $translation->context_en,
            context_ru: $translation->context_ru,
            examples: $this->mapModelExamplesToDto($translation->examples)
        ))->all();
    }

    /**
     * @param Collection<int, TranslationExample> $examples
     * @return TranslationExampleDatasetDto[]
     */
    private function mapModelExamplesToDto(Collection $examples): array
    {
        return $examples->map(fn(TranslationExample $example) => new TranslationExampleDatasetDto(
            sentence_en: $example->sentence_en,
            sentence_ru: $example->sentence_ru,
        ))->all();
    }
}