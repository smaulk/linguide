<?php
declare(strict_types=1);

namespace App\Core\Modules\Words\Mappers;

use App\Core\Modules\Words\Dto\WordTranslationDto;
use App\Core\Modules\Words\Dto\TranslationExampleDto;
use App\Core\Modules\Words\Dto\WordDto;
use App\Core\Modules\Words\Enums\PartOfSpeechType;
use App\Core\Modules\Words\Models\TranslationExample;
use App\Core\Modules\Words\Models\Word;
use App\Core\Modules\Words\Models\WordTranslation;
use Illuminate\Database\Eloquent\Collection;

final class WordTranslationsMapper
{
    //RAW -> DTO

    /**
     * @param array<int, array<string, mixed>> $rawWords
     * @return WordDto[]
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
     * @return WordDto
     */
    public function mapRawToDto(array $rawWord): WordDto
    {
        return new WordDto(
            text: strtolower($rawWord['word']),
            pos: PartOfSpeechType::from(strtolower($rawWord['pos'])),
            translations: $this->mapRawTranslationsToDto($rawWord['translations'] ?? []),
        );
    }

    /**
     * @param array<int, array<string, mixed>> $rawTranslations
     * @return WordTranslationDto[]
     */
    private function mapRawTranslationsToDto(array $rawTranslations): array
    {
        return array_map(fn(array $translation) => new WordTranslationDto(
            text: $translation['translation'],
            context_en: $translation['context_en'],
            context_ru: $translation['context_ru'],
            examples: $this->mapRawExamplesToDto($translation['examples'] ?? [])
        ), $rawTranslations);
    }

    /**
     * @param array<int, array<string, string>> $rawExamples
     * @return TranslationExampleDto[]
     */
    private function mapRawExamplesToDto(array $rawExamples): array
    {
        return array_map(fn(array $example) => new TranslationExampleDto(
            sentence_en: $example['sentence_en'],
            sentence_ru: $example['sentence_ru'],
        ), $rawExamples);
    }



    // DTO -> RAW

    /**
     * @param WordDto[] $dtoWords
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
     * @param WordDto $dtoWord
     * @return array<string, mixed>
     */
    public function mapDtoToRaw(WordDto $dtoWord): array
    {
        return [
            'word'         => $dtoWord->text,
            'pos'          => $dtoWord->pos->value,
            'translations' => $this->mapDtoTranslationsToRaw($dtoWord->translations ?? []),
        ];
    }

    /**
     * @param WordTranslationDto[] $dtoTranslations
     * @return array<int, array<string, mixed>>
     */
    private function mapDtoTranslationsToRaw(array $dtoTranslations): array
    {
        return array_map(fn(WordTranslationDto $translation) => [
            'translation' => $translation->text,
            'context_en'  => $translation->context_en,
            'context_ru'  => $translation->context_ru,
            'examples'    => $this->mapDtoExamplesToRaw($translation->examples ?? []),
        ], $dtoTranslations);
    }

    /**
     * @param TranslationExampleDto[] $dtoExamples
     * @return array<int, array<string, string>>
     */
    private function mapDtoExamplesToRaw(array $dtoExamples): array
    {
        return array_map(fn(TranslationExampleDto $example) => [
            'sentence_en' => $example->sentence_en,
            'sentence_ru' => $example->sentence_ru,
        ], $dtoExamples);
    }



    // MODEL -> DTO

    /**
     * @param Word[] $modelWords
     * @return WordDto[]
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
     * @return WordDto
     */
    public function mapModelToDto(Word $modelWord): WordDto
    {
        return new WordDto(
            text: $modelWord->text,
            pos: $modelWord->pos,
            translations: $this->mapModelTranslationsToDto($modelWord->translations),
        );
    }

    /**
     * @param Collection<int, WordTranslation> $translations
     * @return WordTranslationDto[]
     */
    private function mapModelTranslationsToDto(Collection $translations): array
    {
        return $translations->map(fn(WordTranslation $translation) => new WordTranslationDto(
            text: $translation->text,
            context_en: $translation->context_en,
            context_ru: $translation->context_ru,
            examples: $this->mapModelExamplesToDto($translation->examples)
        ))->all();
    }

    /**
     * @param Collection<int, TranslationExample> $examples
     * @return TranslationExampleDto[]
     */
    private function mapModelExamplesToDto(Collection $examples): array
    {
        return $examples->map(fn(TranslationExample $example) => new TranslationExampleDto(
            sentence_en: $example->sentence_en,
            sentence_ru: $example->sentence_ru,
        ))->all();
    }
}