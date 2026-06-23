<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Jobs;

use App\Core\Common\Parents\Job;
use App\Core\Modules\Ai\Exceptions\AiUnavailableException;
use App\Core\Modules\Term\Actions\TranslateTermsAction;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Throwable;

final class TranslateTermsJob extends Job
{
    /**
     * @throws BindingResolutionException
     * @throws Throwable
     */
    public function handle(TranslateTermsAction $action): void
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