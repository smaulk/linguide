<?php
declare(strict_types=1);

namespace App\Core\Modules\Dictionary\SubActions;

use App\Core\Common\Parents\SubAction;
use App\Core\Modules\Dictionary\Dto\ImportTranslationsResultDto;
use App\Core\Modules\Dictionary\Tasks\ImportTranslationsTask;
use App\Infrastructure\Common\Exceptions\MissingResourceException;
use App\Infrastructure\Modules\Dictionary\Contracts\TranslationsSourceContract;
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