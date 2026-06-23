<?php

namespace App\Core\Modules\Term\Jobs;

use App\Core\Common\Parents\Job;
use App\Core\Modules\Ai\Exceptions\AiUnavailableException;
use App\Core\Modules\Term\Actions\ResolveTermCandidatesAction;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Throwable;

final class ResolveTermCandidatesJob extends Job
{
    /**
     * @throws BindingResolutionException
     * @throws Throwable
     */
    public function handle(ResolveTermCandidatesAction $action): void
    {
        try {
            $action->run();
        } catch (AiUnavailableException) {
            $this->release(900);
            return;
        }
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
