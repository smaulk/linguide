<?php
declare(strict_types=1);

namespace App\Core\Modules\Dictionary\Tasks;

use App\Core\Common\Parents\Task;
use App\Core\Modules\Dictionary\Models\ReviewSessionItem;

final class CreateReviewSessionItemsTask extends Task
{
    /**
     * @param int[] $variantsIds
     */
    public function run(int $sessionId, array $variantsIds): void
    {
        if ($variantsIds === []) {
            return;
        }

        ReviewSessionItem::query()->insert(
            $this->prepareRows($sessionId, $variantsIds)
        );
    }

    /**
     * @param int[] $variantsIds
     * @return array<int, array<string, mixed>>
     */
    private function prepareRows(int $sessionId, array $variantsIds): array
    {
        $rows = [];
        foreach ($variantsIds as $variantId) {
            $rows[] = [
                'session_id' => $sessionId,
                'variant_id' => $variantId,
            ];
        }

        return $rows;
    }
}