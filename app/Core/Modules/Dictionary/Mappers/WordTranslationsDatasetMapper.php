<?php
declare(strict_types=1);

namespace App\Core\Modules\Dictionary\Mappers;

use App\Core\Modules\Dictionary\Dto\WordTranslationDatasetDto;
use App\Core\Modules\Dictionary\Dto\TranslationExampleDatasetDto;
use App\Core\Modules\Dictionary\Dto\WordDatasetDto;
use App\Core\Modules\Dictionary\Enums\PartOfSpeech;
use App\Core\Modules\Dictionary\Models\TranslationExample;
use App\Core\Modules\Dictionary\Models\Word;
use App\Core\Modules\Dictionary\Models\WordTranslation;
use Illuminate\Database\Eloquent\Collection;

final class WordTranslationsDatasetMapper
{
    //RAW -> DTO

    /**
     * @param array<int, array<string, mixed>> $rawWords
     * @return WordDatasetDto[]
     */
    public function mapRawArrayToDtoArray(array $rawWords): array
    {
        $data = [];
        foreach ($rawWords as $rawWord) {
            $data[] = $this->mapRawToDto($rawWord);
        }

        return $data;
    }

    /**
     * @param array<string, mixed> $rawWord
     * @return WordDatasetDto
     */
    public function mapRawToDto(array $rawWord): WordDatasetDto
    {
        return new WordDatasetDto(
            text: strtolower(trim($rawWord['word'])),
            pos: PartOfSpeech::from(strtolower(trim($rawWord['pos']))),
            translations: $this->mapRawTranslationsToDto($rawWord['translations'] ?? []),
        );
    }

    /**
     * @param array<int, array<string, mixed>> $rawTranslations
     * @return WordTranslationDatasetDto[]
     */
    private function mapRawTranslationsToDto(array $rawTranslations): array
    {
        return array_map(fn(array $translation) => new WordTranslationDatasetDto(
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
     * @param WordDatasetDto[] $dtoWords
     * @return array<int, array<string, mixed>>
     */
    public function mapDtoArrayToRawArray(array $dtoWords): array
    {
        $data = [];
        foreach ($dtoWords as $dtoWord) {
            $data[] = $this->mapDtoToRaw($dtoWord);
        }

        return $data;
    }

    /**
     * @param WordDatasetDto $dtoWord
     * @return array<string, mixed>
     */
    public function mapDtoToRaw(WordDatasetDto $dtoWord): array
    {
        return [
            'word'         => $dtoWord->text,
            'pos'          => $dtoWord->pos->value,
            'translations' => $this->mapDtoTranslationsToRaw($dtoWord->translations),
        ];
    }

    /**
     * @param WordTranslationDatasetDto[] $dtoTranslations
     * @return array<int, array<string, mixed>>
     */
    private function mapDtoTranslationsToRaw(array $dtoTranslations): array
    {
        return array_map(fn(WordTranslationDatasetDto $translation) => [
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
     * @param Word[] $modelWords
     * @return WordDatasetDto[]
     */
    public function mapModelArrayToDtoArray(array $modelWords): array
    {
        $data = [];
        foreach ($modelWords as $modelWord) {
            $data[] = $this->mapModelToDto($modelWord);
        }

        return $data;
    }

    /**
     * @param Word $modelWord
     * @return WordDatasetDto
     */
    public function mapModelToDto(Word $modelWord): WordDatasetDto
    {
        return new WordDatasetDto(
            text: $modelWord->text,
            pos: $modelWord->pos,
            translations: $this->mapModelTranslationsToDto($modelWord->translations),
        );
    }

    /**
     * @param Collection<int, WordTranslation> $translations
     * @return WordTranslationDatasetDto[]
     */
    private function mapModelTranslationsToDto(Collection $translations): array
    {
        return $translations->map(fn(WordTranslation $translation) => new WordTranslationDatasetDto(
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