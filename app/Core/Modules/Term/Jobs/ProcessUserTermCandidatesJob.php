<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Jobs;

use App\Core\Common\Parents\Job;
use App\Core\Modules\Term\Actions\ProcessUserTermCandidatesAction;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Throwable;

final class ProcessUserTermCandidatesJob extends Job
{
    /**
     * @throws Throwable
     */
    public function handle(ProcessUserTermCandidatesAction $action): void
    {
        $action->run();
    }

    /**
     * @return mixed[]
     */
    public function middleware(): array
    {
        return [
            new WithoutOverlapping(static::class)
                ->releaseAfter(60)
                ->expireAfter(900),
        ];
    }
}