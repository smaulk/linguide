<?php
declare(strict_types=1);

namespace App\Core\Modules\Dictionary\SubActions;

use App\Core\Common\Parents\SubAction;
use App\Core\Modules\Dictionary\Tasks\ImportWordsTask;
use App\Infrastructure\Common\Exceptions\MissingResourceException;
use App\Infrastructure\Modules\Dictionary\Contracts\WordSourceContract;

final class ImportWordsSubAction extends SubAction
{
    public function __construct(
        private readonly ImportWordsTask $importTask,
        private readonly WordSourceContract $wordSource
    ){}

    /**
     * @param string $resourceName имя ресурса
     * @return int количество импортированных слов
     * @throws MissingResourceException
     */
    public function run(string $resourceName): int
    {
        return $this->importTask->run(
            $this->wordSource->get($resourceName)
        );
    }
}