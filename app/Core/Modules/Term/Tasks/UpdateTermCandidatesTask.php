<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Tasks;

use App\Core\Common\Parents\Task;
use App\Core\Modules\Term\Dto\UpdateCandidateDto;
use App\Core\Modules\Term\Enums\TermCandidateStatus;
use Illuminate\Support\Facades\DB;

final class UpdateTermCandidatesTask extends Task
{
    /**
     * @param UpdateCandidateDto[] $candidates
     */
    public function run(array $candidates): void
    {
        if (empty($candidates)) {
            return;
        }

        [$rows, $values] = $this->prepareData($candidates);

        DB::update(
            $this->getSql($values),
            $rows
        );
    }

    /**
     * @param UpdateCandidateDto[] $candidates
     * @return array{0: array<int, mixed>, 1: string[]}
     */

    private function prepareData(array $candidates): array
    {
        $rows = [];
        $values = [];

        foreach ($candidates as $candidate) {
            $rows[] = $candidate->candidateId;
            $rows[] = $candidate->isValid
                ? TermCandidateStatus::VALID->value
                : TermCandidateStatus::INVALID->value;

            $rows[] = $candidate->termId;

            $values[] = '(?::integer, ?, ?::integer)';
        }

        return [$rows, $values];
    }

    /**
     * @param string[] $values
     */
    private function getSql(array $values): string
    {
        $valuesSql = implode(',', $values);

        return <<<SQL
UPDATE term_candidates tc
SET
    status = v.status,
    term_id = v.term_id,
    updated_at = NOW()
FROM (
    VALUES $valuesSql
) AS v(candidate_id, status, term_id)
WHERE tc.id = v.candidate_id
SQL;
    }
}