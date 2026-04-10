<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\SubActions;

use App\Core\Common\Parents\SubAction;
use App\Core\Modules\Term\Dto\ImportTranslationsResultDto;
use App\Core\Modules\Term\Tasks\ImportTranslationsTask;
use App\Infrastructure\Common\Exceptions\MissingResourceException;
use App\Infrastructure\Modules\Term\Contracts\TranslationsSourceContract;
use Throwable;

final class ImportTranslationsSubAction extends SubAction
{
    public function __construct(
        private readonly ImportTranslationsTask $importTask,
        private readonly TranslationsSourceContract $translationsSource,
    ){}

    /**
     * @param string $resourceName имя ресурса
     * @return ImportTranslationsResultDto
     * @throws Throwable
     * @throws MissingResourceException
     */
    public function run(string $resourceName): ImportTranslationsResultDto
    {
        return $this->importTask->run(
            $this->translationsSource->get($resourceName)
        );
    }
}