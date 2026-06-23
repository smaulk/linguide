<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Tasks;

use App\Core\Common\Parents\Task;
use App\Core\Modules\Term\Dto\StoreTermsResultDto;
use App\Core\Modules\Term\Dto\StoreTermDto;
use Illuminate\Support\Facades\DB;
use LogicException;
use stdClass;
use Throwable;

final class StoreTermsTask extends Task
{
    private const int BATCH_SIZE = 500;

    /**
     * @param iterable<StoreTermDto> $terms
     * @return StoreTermsResultDto
     * @throws Throwable
     */
    public function run(iterable $terms): StoreTermsResultDto
    {
        $stats = ['termsCount' => 0, 'variantsCount' => 0, 'termIdsByText' => []];

        foreach (chunk_iterable($terms, self::BATCH_SIZE) as $batch) {
            $this->handleBatch($batch, $stats);
        }

        return new StoreTermsResultDto(
            termIdsByText: $stats['termIdsByText'],
            termsCount: $stats['termsCount'],
            variantsCount: $stats['variantsCount'],
        );
    }

    /**
     * @param StoreTermDto[] $batch
     * @param array{"termsCount": int, "variantsCount":int, "termIdsByText":array<string,int>} $stats
     * @throws Throwable
     */
    private function handleBatch(array $batch, array &$stats): void
    {
        [$t, $v, $termsMap] = DB::transaction(fn() => $this->processBatch($batch));

        $stats['termsCount'] += $t;
        $stats['variantsCount'] += $v;

        foreach ($termsMap as $text => $id) {
            $stats['termIdsByText'][$text] = $id;
        }
    }

    /**
     * @param StoreTermDto[] $buffer
     * @return array{0:int,1:int,2:array<string, int>}
     */
    private function processBatch(array $buffer): array
    {
        [$termsRows, $termsValues] = $this->prepareTermsData($buffer);
        if (empty($termsRows)) {
            return [0, 0, []];
        }

        $terms = DB::select(
            $this->getUpsertTermsSql($termsValues),
            $termsRows
        );

        $termsMap = $this->buildTermsMap($terms);

        [$variantsRows, $variantsValues] = $this->prepareVariantsData($buffer, $termsMap);
        if (empty($variantsRows)) {
            throw new LogicException('Terms must have variants.');
        }

        DB::insert(
            $this->getInsertVariantsSql($variantsValues),
            $variantsRows
        );

        return [
            count($terms),
            count($variantsValues),
            $termsMap,
        ];
    }

    /**
     * @param StoreTermDto[] $dtoTerms
     * @return array{0: array<int, mixed>, 1: string[]}
     */
    private function prepareTermsData(array $dtoTerms): array
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
            $rows[] = $termDto->isVerified;

            $values[] = '(?, ?, ?::boolean)';
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
     * @param StoreTermDto[] $dtoTerms
     * @param array<string, int> $termsMap
     * @return array{0: array<int, mixed>, 1: string[]}
     */
    private function prepareVariantsData(array $dtoTerms, array $termsMap): array
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
            $rows[] = $termDto->level?->value;

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
WITH input(text, type, is_verified) AS (
    VALUES $valuesSql
),
upsert AS (
    INSERT INTO terms (text, type, is_verified, created_at, updated_at)
    SELECT text, type, is_verified, NOW(), NOW()
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
INSERT INTO term_variants (term_id, pos, level, created_at)
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