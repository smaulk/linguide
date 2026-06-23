<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Tasks;

use App\Core\Common\Parents\Task;
use App\Core\Modules\Term\Models\UserTermCandidate;

final class StoreUserTermCandidatesTask extends Task
{
    /**
     * @param int[] $candidateIds
     */
    public function run(int $userId, array $candidateIds): void
    {
        if (empty($candidateIds)) {
            return;
        }

        UserTermCandidate::query()->insertOrIgnore(
            $this->prepareData($userId, $candidateIds)
        );
    }

    /**
     * @param int[] $candidateIds
     * @return array<string, mixed>
     */
    private function prepareData(int $userId, array $candidateIds): array
    {
        $now = now();

        return array_map(fn(int $candidateId) => [
            'user_id'      => $userId,
            'candidate_id' => $candidateId,
            'is_processed' => false,
            'created_at'   => $now,
            'updated_at'   => $now,
        ], $candidateIds);
    }
}