<?php
declare(strict_types=1);

namespace App\Core\Modules\Words\Actions;

use App\Core\Common\Parents\Action;
use App\Core\Modules\Words\Dto\ImportWordTranslationsResultDto;
use App\Core\Modules\Words\Tasks\ImportWordTranslationsTask;
use App\Infrastructure\Learning\Sources\Contracts\WordTranslationsSourceContract;
use Throwable;

final class ImportWordTranslationsAction extends Action
{
    public function __construct(
        private readonly ImportWordTranslationsTask $importTask,
        private readonly WordTranslationsSourceContract $translationsSource,
    ){}

    /**
     * @throws Throwable
     */
    public function run(string $resourceName): ImportWordTranslationsResultDto
    {
        $words = $this->translationsSource->get($resourceName);

        return $this->importTask->run($words);
    }
}