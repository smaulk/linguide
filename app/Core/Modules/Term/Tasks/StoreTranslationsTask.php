<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Tasks;

use App\Core\Common\Parents\Task;
use App\Core\Modules\Term\Dto\StoreTranslationsResultDto;
use App\Core\Modules\Term\Dto\StoreTranslationTermDto;
use App\Core\Modules\Term\Models\TermVariant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use stdClass;
use Throwable;

final class StoreTranslationsTask extends Task
{
    private const int BATCH_SIZE = 500;

    /**
     * @param iterable<StoreTranslationTermDto> $terms
     * @return StoreTranslationsResultDto
     * @throws Throwable
     */
    public function run(iterable $terms): StoreTranslationsResultDto
    {
        $stats = ['variants' => 0, 'translations' => 0, 'examples' => 0];

        foreach (chunk_iterable($terms, self::BATCH_SIZE) as $batch) {
            $this->handleBatch($batch, $stats);
        }

        return new StoreTranslationsResultDto(
            variantsCount: $stats['variants'],
            translationsCount: $stats['translations'],
            examplesCount: $stats['examples'],
        );
    }

    /**
     * @param StoreTranslationTermDto[] $batch
     * @param array{"variants": int, "translations": int, "examples": int} $stats
     * @throws Throwable
     */
    private function handleBatch(array $batch, array &$stats): void
    {
        [$v, $t, $e] = DB::transaction(fn() => $this->processBatch($batch));

        $stats['variants'] += $v;
        $stats['translations'] += $t;
        $stats['examples'] += $e;
    }

    /**
     * @param StoreTranslationTermDto[] $dtoTerms
     * @return array{0:int,1:int,2:int}
     * @throws Throwable
     */
    private function processBatch(array $dtoTerms): array
    {
        $variants = $this->loadVariants($dtoTerms);

        [$translationsRows, $translationsValues] =
            $this->prepareTranslationsData($variants, $dtoTerms);

        if (empty($translationsRows)) {
            return [0, 0, 0];
        }

        $translations = DB::select(
            $this->getInsertTranslationsSql($translationsValues),
            $translationsRows
        );

        $translationsMap = $this->buildTranslationsMap($translations);

        [$examplesRows, $examplesValues] =
            $this->prepareExamplesData($variants, $dtoTerms, $translationsMap);

        if (!empty($examplesRows)) {
            DB::insert(
                $this->getInsertExamplesSql($examplesValues),
                $examplesRows
            );
        }

        return [
            count($dtoTerms),
            count($translations),
            count($examplesValues),
        ];
    }

    /**
     * @param StoreTranslationTermDto[] $dtoTerms
     * @return Collection<string, TermVariant>
     */
    private function loadVariants(array $dtoTerms): Collection
    {
        $termsText = array_map(fn(StoreTranslationTermDto $dto) => $dto->text, $dtoTerms);

        return TermVariant::query()
            ->select(['id', 'term_id', 'pos'])
            ->whereHas('term', fn($q) => $q->whereIn('text', $termsText))
            ->with(['term:id,text'])
            ->get()
            ->keyBy(fn(TermVariant $variant) => $this->variantKey($variant->term->text, $variant->pos->value));
    }

    /**
     * @param Collection<string, TermVariant> $variants
     * @param StoreTranslationTermDto[] $dtoTerms
     * @return array{0: array<int, mixed>, 1: string[]}
     */
    private function prepareTranslationsData(Collection $variants, array $dtoTerms): array
    {
        $rows = [];
        $values = [];

        foreach ($dtoTerms as $termDto) {
            $variant = $variants->get($this->variantKey($termDto->text, $termDto->pos->value));
            if ($variant === null) {
                continue;
            }

            foreach ($termDto->translations as $translationDto) {
                $rows[] = $variant->id;
                $rows[] = $translationDto->text;
                $rows[] = $translationDto->contextEn;
                $rows[] = $translationDto->contextRu;

                $values[] = '(?::integer, ?, ?, ?)';
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
            $key = $this->translationKey($row->variant_id, $row->text, $row->context_en);
            $map[$key] = $row;
        }

        return $map;
    }

    /**
     * @param Collection<string, TermVariant> $variants
     * @param StoreTranslationTermDto[] $dtoTerms
     * @param array<string, stdClass> $translationsMap
     * @return array{0: array<int, mixed>, 1: string[]}
     */
    private function prepareExamplesData(Collection $variants, array $dtoTerms, array $translationsMap): array
    {
        $rows = [];
        $values = [];

        foreach ($dtoTerms as $termDto) {
            $variant = $variants->get($this->variantKey($termDto->text, $termDto->pos->value));
            if ($variant === null) {
                continue;
            }

            foreach ($termDto->translations as $translationDto) {
                $key = $this->translationKey($variant->id, $translationDto->text, $translationDto->contextEn);
                $translation = $translationsMap[$key] ?? null;
                if ($translation === null) {
                    continue;
                }

                foreach ($translationDto->examples as $exampleDto) {
                    $rows[] = $translation->id;
                    $rows[] = $exampleDto->sentenceEn;
                    $rows[] = $exampleDto->sentenceRu;

                    $values[] = '(?::integer, ?, ?)';
                }
            }
        }

        return [$rows, $values];
    }

    private function variantKey(string $term, string $pos): string
    {
        return $term . '|' . $pos;
    }

    private function translationKey(int $variantId, string $text, string $context): string
    {
        return $variantId . '|' . $text . '|' . $context;
    }

    /**
     * @param string[] $values
     */
    private function getInsertTranslationsSql(array $values): string
    {
        $valuesSql = $this->implodeSqlValues($values);

        return <<<SQL
INSERT INTO translations
(variant_id, text, context_en, context_ru, created_at)
SELECT t.variant_id, t.translation, t.context_en, t.context_ru, NOW()
FROM (VALUES $valuesSql)
AS t(variant_id, translation, context_en, context_ru)
ON CONFLICT (variant_id, text, context_en) DO NOTHING
RETURNING id, variant_id, text, context_en;
SQL;
    }

    /**
     * @param string[] $values
     */
    private function getInsertExamplesSql(array $values): string
    {
        $valuesSql = $this->implodeSqlValues($values);

        return <<<SQL
INSERT INTO translation_examples
(translation_id, sentence_en, sentence_ru, created_at)
SELECT e.translation_id, e.sentence_en, e.sentence_ru, NOW()
FROM (VALUES $valuesSql)
AS e(translation_id, sentence_en, sentence_ru)
SQL;
    }

    /**
     * @param string[] $values
     */
    private function implodeSqlValues(array $values): string
    {
        return implode(',', $values);
    }
}