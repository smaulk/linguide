<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Tasks;

use App\Core\Common\Parents\Task;
use App\Core\Modules\Term\Enums\TermCandidateStatus;
use Illuminate\Support\Facades\DB;

final class StoreTermCandidatesTask extends Task
{
    /**
     * @param string[] $terms
     * @return int[]
     */
    public function run(array $terms): array
    {
        if (empty($terms)) {
            return [];
        }

        [$rows, $values] = $this->prepareData($terms);

        $candidates = DB::select(
            $this->getInsertCandidatesSql($values),
            $rows
        );

        return array_column($candidates, 'id');
    }

    /**
     * @param string[] $terms
     * @return array{0: string[], 1: string[]}
     */
    private function prepareData(array $terms): array
    {
        $rows = [];
        $values = [];

        foreach ($terms as $term) {
            $rows[] = $term;
            $rows[] = TermCandidateStatus::PENDING->value;

            $values[] = '(?, ?)';
        }

        return [$rows, $values];
    }

    /**
     * @param string[] $values
     * @return string
     */
    private function getInsertCandidatesSql(array $values): string
    {
        $valuesSql = implode(',', $values);

        return <<<SQL
INSERT INTO term_candidates (raw_term, status, created_at, updated_at)
SELECT c.raw_term, c.status, NOW(), NOW()
FROM (VALUES $valuesSql) 
AS c(raw_term, status)
ON CONFLICT (raw_term)
DO UPDATE SET raw_term = EXCLUDED.raw_term
RETURNING id
SQL;
    }
}