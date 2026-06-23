<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\SubActions;

use App\Core\Common\Parents\SubAction;
use App\Core\Modules\Term\Tasks\StoreTermCandidatesTask;
use App\Core\Modules\Term\Tasks\StoreUserTermCandidatesTask;
use Illuminate\Support\Facades\DB;
use Throwable;

final class StoreTermCandidatesForUserSubAction extends SubAction
{
    public function __construct(
        private readonly StoreTermCandidatesTask $storeCandidatesTask,
        private readonly StoreUserTermCandidatesTask $storeUserCandidatesTask,
    ){}

    /**
     * @param string[] $terms
     * @throws Throwable
     */
    public function run(int $userId, array $terms): void
    {
        DB::transaction(function () use ($userId, $terms) {
            $candidateIds = $this->storeCandidatesTask->run($terms);
            $this->storeUserCandidatesTask->run($userId, $candidateIds);
        });
    }
}