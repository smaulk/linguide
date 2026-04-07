<?php
declare(strict_types=1);

namespace App\Core\Modules\Dictionary\Tasks;

use App\Core\Common\Parents\Task;
use App\Core\Modules\Dictionary\Models\WordReviewSessionItem;

final class CreateWordReviewSessionItemsTask extends Task
{
    /**
     * @param int[] $wordIds
     */
    public function run(int $sessionId, array $wordIds): void
    {
        if ($wordIds === []) {
            return;
        }

        $rows = $this->prepareRows($sessionId, $wordIds);
        WordReviewSessionItem::query()->insert($rows);
    }

    /**
     * @param int[] $wordIds
     * @return array<int, array<string, mixed>>
     */
    private function prepareRows(int $sessionId, array $wordIds): array
    {
        $rows = [];
        foreach ($wordIds as $wordId) {
            $rows[] = [
                'session_id' => $sessionId,
                'word_id'    => $wordId,
            ];
        }

        return $rows;
    }
}