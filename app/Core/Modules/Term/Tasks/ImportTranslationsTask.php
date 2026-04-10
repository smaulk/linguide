<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Tasks;

use App\Core\Common\Parents\Task;
use App\Core\Modules\Term\Dto\ImportTranslationsResultDto;
use App\Core\Modules\Term\Dto\TermTranslationDatasetDto;
use App\Core\Modules\Term\Models\TermVariant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use stdClass;
use Throwable;

final class ImportTranslationsTask extends Task
{
    private const int BATCH_SIZE = 500;

    /**
     * @param iterable<TermTranslationDatasetDto> $terms
     * @return ImportTranslationsResultDto
     * @throws Throwable
     */
    public function run(iterable $terms): ImportTranslationsResultDto
    {
        $buffer = [];
        $stats = ['variants' => 0, 'translations' => 0, 'examples' => 0];

        foreach ($terms as $termDto) {
            $buffer[] = $termDto;

            if (count($buffer) >= self::BATCH_SIZE) {
                $this->handleBatch($buffer, $stats);
                $buffer = [];
            }
        }

        if (!empty($buffer)) {
            $this->handleBatch($buffer, $stats);
        }

        return new ImportTranslationsResultDto(
            variants: $stats['variants'],
            translations: $stats['translations'],
            examples: $stats['examples'],
        );
    }

    /**
     * @param TermTranslationDatasetDto[] $buffer
     * @param array{"variants": int, "translations": int, "examples": int} $stats
     * @throws Throwable
     */
    private function handleBatch(array $buffer, array &$stats): void
    {
        [$v, $t, $e] = DB::transaction(fn() => $this->processBatch($buffer));

        $stats['variants'] += $v;
        $stats['translations'] += $t;
        $stats['examples'] += $e;
    }

    /**
     * @param TermTranslationDatasetDto[] $dtoTerms
     * @return array{0:int,1:int,2:int}
     * @throws Throwable
     */
    private function processBatch(array $dtoTerms): array
    {
        $variants = $this->loadVariants($dtoTerms);

        [$translationsRows, $translationsValues] =
            $this->buildTranslationsData($variants, $dtoTerms);

        if (empty($translationsRows)) {
            return [0, 0, 0];
        }

        $translations = DB::select(
            $this->getInsertTranslationsSql($translationsValues),
            $translationsRows
        );

        $translationsMap = $this->buildTranslationsMap($translations);

        [$examplesRows, $examplesValues] =
            $this->buildExamplesData($variants, $dtoTerms, $translationsMap);

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
     * @param TermTranslationDatasetDto[] $dtoTerms
     * @return Collection<string, TermVariant>
     */
    private function loadVariants(array $dtoTerms): Collection
    {
        $termsText = array_map(fn(TermTranslationDatasetDto $dto) => $dto->text, $dtoTerms);

        return TermVariant::query()
            ->select(['id', 'term_id', 'pos'])
            ->whereHas('term', fn ($q) => $q->whereIn('text', $termsText))
            ->with(['term:id,text'])
            ->get()
            ->keyBy(fn(TermVariant $variant) => $this->variantKey($variant->term->text, $variant->pos->value));
    }

    /**
     * @param Collection<string, TermVariant> $variants
     * @param TermTranslationDatasetDto[] $dtoTerms
     * @return array{0: array<int, mixed>, 1: string[]}
     */
    private function buildTranslationsData(Collection $variants, array $dtoTerms): array
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
                $rows[] = $translationDto->context_en;
                $rows[] = $translationDto->context_ru;

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
     * @param TermTranslationDatasetDto[] $dtoTerms
     * @param array<string, stdClass> $translationsMap
     * @return array{0: array<int, mixed>, 1: string[]}
     */
    private function buildExamplesData(Collection $variants, array $dtoTerms, array $translationsMap): array
    {
        $rows = [];
        $values = [];

        foreach ($dtoTerms as $termDto) {
            $variant = $variants->get($this->variantKey($termDto->text, $termDto->pos->value));
            if ($variant === null) {
                continue;
            }

            foreach ($termDto->translations as $translationDto) {
                $key = $this->translationKey($variant->id, $translationDto->text, $translationDto->context_en);
                $translation = $translationsMap[$key] ?? null;
                if ($translation === null) {
                    continue;
                }

                foreach ($translationDto->examples as $exampleDto) {
                    $rows[] = $translation->id;
                    $rows[] = $exampleDto->sentence_en;
                    $rows[] = $exampleDto->sentence_ru;

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