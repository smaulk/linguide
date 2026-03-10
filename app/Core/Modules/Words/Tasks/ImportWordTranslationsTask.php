<?php
declare(strict_types=1);

namespace App\Core\Modules\Words\Tasks;

use App\Core\Common\Parents\Task;
use App\Core\Modules\Words\Dto\ImportWordTranslationsResultDto;
use App\Core\Modules\Words\Dto\WordDatasetDto;
use App\Core\Modules\Words\Models\Word;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use stdClass;
use Throwable;

final class ImportWordTranslationsTask extends Task
{
    private const int BATCH_SIZE = 500;

    /**
     * @param iterable<WordDatasetDto> $words
     * @return ImportWordTranslationsResultDto
     * @throws Throwable
     */
    public function run(iterable $words): ImportWordTranslationsResultDto
    {
        $buffer = [];
        $stats = ['words' => 0, 'translations' => 0, 'examples' => 0];

        foreach ($words as $wordDto) {
            $buffer[] = $wordDto;

            if (count($buffer) >= self::BATCH_SIZE) {
                $this->handleBatch($buffer, $stats);
                $buffer = [];
            }
        }

        if (!empty($buffer)) {
            $this->handleBatch($buffer, $stats);
        }

        return new ImportWordTranslationsResultDto(
            words: $stats['words'],
            translations: $stats['translations'],
            examples: $stats['examples'],
        );
    }

    /**
     * @param WordDatasetDto[] $buffer
     * @param array{"words": int, "translations": int, "examples": int} $stats
     * @throws Throwable
     */
    private function handleBatch(array $buffer, array &$stats): void
    {
        [$w, $t, $e] = $this->processBatchTransaction($buffer);

        $stats['words'] += $w;
        $stats['translations'] += $t;
        $stats['examples'] += $e;
    }

    /**
     * @param WordDatasetDto[] $dtoWords
     * @return array{0:int,1:int,2:int}
     * @throws Throwable
     */
    private function processBatchTransaction(array $dtoWords): array
    {
        return DB::transaction(function () use ($dtoWords): array {
            return $this->processBatch($dtoWords);
        });
    }

    /**
     * @param WordDatasetDto[] $dtoWords
     * @return array{0:int,1:int,2:int}
     * @throws Throwable
     */
    private function processBatch(array $dtoWords): array
    {
        $words = $this->loadWords($dtoWords);

        [$translationsRows, $translationsValues] =
            $this->buildTranslationsData($words, $dtoWords);

        if (empty($translationsRows)) {
            return [0, 0, 0];
        }

        $translations = DB::select(
            $this->getInsertTranslationsSql($translationsValues),
            $translationsRows
        );

        $translationsMap = $this->buildTranslationsMap($translations);

        [$examplesRows, $examplesValues] =
            $this->buildExamplesData($words, $dtoWords, $translationsMap);

        if (!empty($examplesRows)) {
            DB::insert(
                $this->getInsertExamplesSql($examplesValues),
                $examplesRows
            );
        }

        return [
            count($dtoWords),
            count($translations),
            count($examplesValues),
        ];
    }

    /**
     * @param WordDatasetDto[] $dtoWords
     * @return Collection<string, Word>
     */
    private function loadWords(array $dtoWords): Collection
    {
        $textWords = array_map(fn(WordDatasetDto $dto) => $dto->text, $dtoWords);

        return Word::query()
            ->select(['id', 'text', 'pos'])
            ->whereIn('text', $textWords)
            ->get()
            ->keyBy(fn(Word $word) => $this->wordKey($word->text, $word->pos->value));
    }


    /**
     * @param Collection<string, Word> $words
     * @param WordDatasetDto[] $dtoWords
     * @return array{0: array<int, mixed>, 1: string[]}
     */
    private function buildTranslationsData(Collection $words, array $dtoWords): array
    {
        $rows = [];
        $values = [];
        foreach ($dtoWords as $wordDto) {
            $word = $words->get($this->wordKey($wordDto->text, $wordDto->pos->value));
            if (!$word) {
                continue;
            }

            foreach ($wordDto->translations ?? [] as $rank => $translationDto) {
                $rows[] = $word->id;
                $rows[] = $translationDto->text;
                $rows[] = $rank + 1;
                $rows[] = $translationDto->context_en;
                $rows[] = $translationDto->context_ru;

                $values[] = '(?::integer, ?, ?::integer, ?, ?)';
            }
        }

        return [$rows, $values];
    }

    /**
     * @param stdClass[] $translations
     * @return array<string, stdClass>
     */
    private function buildTranslationsMap(array $translations): array
    {
        $map = [];
        foreach ($translations as $row) {
            $key = $this->translationKey($row->word_id, $row->text, $row->context_en);
            $map[$key] = $row;
        }

        return $map;
    }

    /**
     * @param Collection<string, Word> $words
     * @param WordDatasetDto[] $dtoWords
     * @param array<string, stdClass> $translationsMap
     * @return array{0: array<int, mixed>, 1: string[]}
     */
    private function buildExamplesData(Collection $words, array $dtoWords, array $translationsMap): array
    {
        $rows = [];
        $values = [];
        foreach ($dtoWords as $wordDto) {
            $word = $words->get($this->wordKey($wordDto->text, $wordDto->pos->value));
            if (!$word) {
                continue;
            }

            foreach ($wordDto->translations ?? [] as $translationDto) {
                $key = $this->translationKey($word->id, $translationDto->text, $translationDto->context_en);
                $translation = $translationsMap[$key] ?? null;
                if (!$translation) {
                    continue;
                }

                foreach ($translationDto->examples ?? [] as $example) {
                    $rows[] = $translation->id;
                    $rows[] = $example->sentence_en;
                    $rows[] = $example->sentence_ru;

                    $values[] = '(?::integer, ?, ?)';
                }
            }
        }

        return [$rows, $values];
    }

    private function wordKey(string $word, string $pos): string
    {
        return $word . '|' . $pos;
    }

    private function translationKey(int $wordId, string $text, string $context): string
    {
        return $wordId . '|' . $text . '|' . $context;
    }

    /**
     * @param string[] $values
     */
    private function getInsertTranslationsSql(array $values): string
    {
        $valuesSql = $this->implodeSqlValues($values);

        return "
INSERT INTO word_translations
(word_id, text, rank, context_en, context_ru, created_at)
SELECT t.word_id, t.translation, t.rank, t.context_en, t.context_ru, NOW()
FROM (VALUES $valuesSql)
AS t(word_id, translation, rank, context_en, context_ru)
RETURNING id, word_id, text, rank, context_en;
";
    }

    /**
     * @param string[] $values
     */
    private function getInsertExamplesSql(array $values): string
    {
        $valuesSql = $this->implodeSqlValues($values);

        return "
INSERT INTO translation_examples
(translation_id, sentence_en, sentence_ru, created_at)
SELECT e.translation_id, e.sentence_en, e.sentence_ru, NOW()
FROM (VALUES $valuesSql)
AS e(translation_id, sentence_en, sentence_ru)
";
    }

    /**
     * @param string[] $values
     */
    private function implodeSqlValues(array $values): string
    {
        return implode(',', $values);
    }
}