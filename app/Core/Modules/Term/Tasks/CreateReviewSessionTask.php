<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Tasks;

use App\Core\Common\Parents\Task;
use App\Core\Modules\Term\Enums\ReviewSessionStatus;
use App\Core\Modules\Term\Models\ReviewSession;
use Throwable;

final class CreateReviewSessionTask extends Task
{
    /**
     * @throws Throwable
     */
    public function run(int $userId): ReviewSession
    {
        $session = new ReviewSession();

        $session->user_id = $userId;
        $session->status = ReviewSessionStatus::ACTIVE;
        $session->started_at = now();

        $session->saveOrFail();

        return $session;
    }
}