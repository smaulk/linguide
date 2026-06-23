<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\SubActions;

use App\Core\Common\Parents\SubAction;
use App\Core\Modules\Term\Dto\StoreTermsResultDto;
use App\Core\Modules\Term\Tasks\StoreTermsTask;
use App\Infrastructure\Common\Exceptions\MissingResourceException;
use App\Infrastructure\Modules\Term\Contracts\TermsSourceContract;
use Throwable;

final class ImportTermsSubAction extends SubAction
{
    public function __construct(
        private readonly StoreTermsTask $storeTask,
        private readonly TermsSourceContract $termsSource
    ){}

    /**
     * @param string $resourceName имя ресурса
     * @return StoreTermsResultDto
     * @throws Throwable
     * @throws MissingResourceException
     */
    public function run(string $resourceName): StoreTermsResultDto
    {
        return $this->storeTask->run(
            $this->termsSource->get($resourceName)
        );
    }
}