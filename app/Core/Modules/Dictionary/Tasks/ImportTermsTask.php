<?php
declare(strict_types=1);

namespace App\Core\Modules\Dictionary\Tasks;

use App\Core\Common\Parents\Task;
use App\Core\Modules\Dictionary\Dto\ImportTermsResultDto;
use App\Core\Modules\Dictionary\Dto\TermDatasetDto;
use Illuminate\Support\Facades\DB;
use LogicException;
use stdClass;
use Throwable;

final class ImportTermsTask extends Task
{
    private const int BATCH_SIZE = 500;

    /**
     * @param iterable<TermDatasetDto> $dtoTerms
     * @return ImportTermsResultDto
     * @throws Throwable
     */
    public function run(iterable $dtoTerms): ImportTermsResultDto
    {
        $buffer = [];
        $stats = ['terms' => 0, 'variants' => 0];

        foreach ($dtoTerms as $dto) {
            $buffer[] = $dto;

            if (count($buffer) >= self::BATCH_SIZE) {
                $this->handleBatch($buffer, $stats);
                $buffer = [];
            }
        }

        if (!empty($buffer)) {
            $this->handleBatch($buffer, $stats);
        }

        return new ImportTermsResultDto(
            terms: $stats['terms'],
            variants: $stats['variants']
        );
    }

    /**
     * @param TermDatasetDto[] $buffer
     * @param array{"terms": int, "variants":int} $stats
     * @throws Throwable
     */
    private function handleBatch(array $buffer, array &$stats): void
    {
        [$t, $v] = DB::transaction(fn() => $this->processBatch($buffer));

        $stats['terms'] += $t;
        $stats['variants'] += $v;
    }

    /**
     * @param TermDatasetDto[] $buffer
     * @return array{0:int,1:int}
     */
    private function processBatch(array $buffer): array
    {
        [$termsRows, $termsValues] = $this->buildTermsData($buffer);
        if (empty($termsRows)) {
            return [0, 0];
        }

        $terms = DB::select(
            $this->getUpsertTermsSql($termsValues),
            $termsRows
        );

        $termsMap = $this->buildTermsMap($terms);

        [$variantsRows, $variantsValues] = $this->buildVariantsData($buffer, $termsMap);
        if (empty($variantsRows)) {
            throw new LogicException('Terms must have variants.');
        }

        DB::insert(
            $this->getInsertVariantsSql($variantsValues),
            $variantsRows
        );

        return [count($terms), count($variantsValues)];
    }

    /**
     * @param TermDatasetDto[] $dtoTerms
     * @return array{0: array<int, mixed>, 1: string[]}
     */
    private function buildTermsData(array $dtoTerms): array
    {
        $rows = [];
        $values = [];
        $seen = [];

        foreach ($dtoTerms as $termDto) {
            // Термин должен быть уникальным (проверка для одного батча)
            if (isset($seen[$termDto->text])) {
                continue;
            }
            $seen[$termDto->text] = true;

            $rows[] = $termDto->text;
            $rows[] = $termDto->type->value;

            $values[] = '(?, ?)';
        }

        return [$rows, $values];
    }

    /**
     * @param stdClass[] $terms
     * @return array<string, int>
     */
    private function buildTermsMap(array $terms): array
    {
        $map = [];
        foreach ($terms as $term) {
            $map[$term->text] = $term->id;
        }

        return $map;
    }

    /**
     * @param TermDatasetDto[] $dtoTerms
     * @param array<string, int> $termsMap
     * @return array{0: array<int, mixed>, 1: string[]}
     */
    private function buildVariantsData(array $dtoTerms, array $termsMap): array
    {
        $rows = [];
        $values = [];

        foreach ($dtoTerms as $termDto) {
            $termId = $termsMap[$termDto->text] ?? null;
            if ($termId === null) {
                continue;
            }

            $rows[] = $termId;
            $rows[] = $termDto->pos->value;
            $rows[] = $termDto->level->value;

            $values[] = '(?::integer, ?, ?::integer)';
        }

        return [$rows, $values];
    }

    /**
     * Добавляет записи в таблицу terms.
     * Игнорирует добавление при наличии существующего термина (может создасться в предыдущем батче).
     * Возвращает id и text добавленных и существующих терминов.
     *
     * @param string[] $values
     */
    private function getUpsertTermsSql(array $values): string
    {
        $valuesSql = $this->implodeSqlValues($values);

        return <<<SQL
WITH input(text, type) AS (
    VALUES $valuesSql
),
upsert AS (
    INSERT INTO terms (text, type, is_verified, created_at, updated_at)
    SELECT text, type, true, NOW(), NOW()
    FROM input
    ON CONFLICT (text)
    DO UPDATE SET text = EXCLUDED.text
    RETURNING id, text
)
SELECT id, text FROM upsert;
SQL;
    }

    /**
     * Добавляет записи в таблицу term_variants.
     * Игнорирует добавление при наличии существующего варианта.
     *
     * @param string[] $values
     */
    private function getInsertVariantsSql(array $values): string
    {
        $valuesSql = $this->implodeSqlValues($values);

        return <<<SQL
INSERT INTO term_variants
(term_id, pos, level, created_at)
SELECT v.term_id, v.pos, v.level, NOW()
FROM (VALUES $valuesSql)
AS v(term_id, pos, level)
ON CONFLICT (term_id, pos) DO NOTHING
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