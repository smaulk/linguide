<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\SubActions;

use App\Core\Common\Parents\SubAction;
use App\Core\Modules\Term\Dto\ImportTermsResultDto;
use App\Core\Modules\Term\Tasks\ImportTermsTask;
use App\Infrastructure\Common\Exceptions\MissingResourceException;
use App\Infrastructure\Modules\Term\Contracts\TermsSourceContract;
use Throwable;

final class ImportTermsSubAction extends SubAction
{
    public function __construct(
        private readonly ImportTermsTask $importTask,
        private readonly TermsSourceContract $termsSource
    ){}

    /**
     * @param string $resourceName имя ресурса
     * @return ImportTermsResultDto
     * @throws Throwable
     * @throws MissingResourceException
     */
    public function run(string $resourceName): ImportTermsResultDto
    {
        return $this->importTask->run(
            $this->termsSource->get($resourceName)
        );
    }
}