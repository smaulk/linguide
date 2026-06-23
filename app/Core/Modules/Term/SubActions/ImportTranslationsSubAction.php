<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\SubActions;

use App\Core\Common\Parents\SubAction;
use App\Core\Modules\Term\Dto\StoreTranslationsResultDto;
use App\Core\Modules\Term\Tasks\StoreTranslationsTask;
use App\Infrastructure\Common\Exceptions\MissingResourceException;
use App\Infrastructure\Modules\Term\Contracts\TranslationsSourceContract;
use Throwable;

final class ImportTranslationsSubAction extends SubAction
{
    public function __construct(
        private readonly StoreTranslationsTask $storeTask,
        private readonly TranslationsSourceContract $translationsSource,
    ){}

    /**
     * @param string $resourceName имя ресурса
     * @return StoreTranslationsResultDto
     * @throws Throwable
     * @throws MissingResourceException
     */
    public function run(string $resourceName): StoreTranslationsResultDto
    {
        return $this->storeTask->run(
            $this->translationsSource->get($resourceName)
        );
    }
}