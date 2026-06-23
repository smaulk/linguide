<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Actions;

use App\Core\Common\Parents\Action;
use App\Core\Modules\Ai\Exceptions\AiUnavailableException;
use App\Core\Modules\Term\Dto\StoreTranslationsResultDto;
use App\Core\Modules\Term\Tasks\GenerateTranslationsTask;
use App\Core\Modules\Term\Tasks\StoreTranslationsTask;
use Illuminate\Contracts\Container\BindingResolutionException;
use Throwable;

final class TranslateTermsAction extends Action
{
    public function __construct(
        private readonly GenerateTranslationsTask $generateTask,
        private readonly StoreTranslationsTask $storeTask,
    ){}

    /**
     * @throws BindingResolutionException
     * @throws Throwable
     * @throws AiUnavailableException
     */
    public function run(): StoreTranslationsResultDto
    {
        return $this->storeTask->run(
            $this->generateTask->run(isOnlyEmpty: true)
        );
    }
}