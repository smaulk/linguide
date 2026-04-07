<?php
declare(strict_types=1);

namespace App\Core\Modules\Dictionary\SubActions;

use App\Core\Common\Parents\SubAction;
use App\Core\Modules\Dictionary\Dto\ImportWordTranslationsResultDto;
use App\Core\Modules\Dictionary\Tasks\ImportWordTranslationsTask;
use App\Infrastructure\Common\Exceptions\MissingResourceException;
use App\Infrastructure\Modules\Dictionary\Contracts\WordTranslationsSourceContract;
use Throwable;

final class ImportWordTranslationsSubAction extends SubAction
{
    public function __construct(
        private readonly ImportWordTranslationsTask $importTask,
        private readonly WordTranslationsSourceContract $translationsSource,
    ){}

    /**
     * @param string $resourceName имя ресурса
     * @throws Throwable|MissingResourceException
     */
    public function run(string $resourceName): ImportWordTranslationsResultDto
    {
        return $this->importTask->run(
            $this->translationsSource->get($resourceName)
        );
    }
}