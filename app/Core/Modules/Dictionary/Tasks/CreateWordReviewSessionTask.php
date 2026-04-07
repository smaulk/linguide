<?php
declare(strict_types=1);

namespace App\Core\Modules\Dictionary\Tasks;

use App\Core\Common\Parents\Task;
use App\Core\Modules\Dictionary\Enums\WordReviewSessionStatus;
use App\Core\Modules\Dictionary\Models\WordReviewSession;
use Throwable;

final class CreateWordReviewSessionTask extends Task
{
    /**
     * @throws Throwable
     */
    public function run(int $userId): WordReviewSession
    {
        $session = new WordReviewSession();

        $session->user_id = $userId;
        $session->status = WordReviewSessionStatus::ACTIVE;
        $session->started_at = now();

        $session->saveOrFail();

        return $session;
    }
}