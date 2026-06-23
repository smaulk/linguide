<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Tasks;

use App\Core\Common\Parents\Task;
use App\Core\Modules\Term\Dto\StoreLearningProgressDto;
use App\Core\Modules\Term\Models\LearningProgress;

final class StoreLearningProgressTask extends Task
{
    /**
     * @param StoreLearningProgressDto[] $items
     */
    public function run(array $items): void
    {
        if (empty($items)) {
            return;
        }

        LearningProgress::query()->insertOrIgnore(
            $this->prepareData($items)
        );
    }

    /**
     * @param StoreLearningProgressDto[] $items
     * @return list<array<string, mixed>>
     */
    private function prepareData(array $items): array
    {
        $now = now();
        $data = [];

        foreach ($items as $item) {
            foreach ($item->termVariantIds as $varId) {
                $data[] = [
                    'user_id'     => $item->userId,
                    'variant_id'  => $varId,
                    'ease_factor' => LearningProgress::DEFAULT_EASE_FACTOR,
                    'repetitions' => 0,
                    'interval'    => 0,
                    'due_at'      => $now,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ];
            }
        }

        return $data;
    }
}