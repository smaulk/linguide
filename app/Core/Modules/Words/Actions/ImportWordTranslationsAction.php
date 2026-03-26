<?php
declare(strict_types=1);

namespace App\Core\Modules\Words\Actions;

use App\Core\Common\Parents\Action;
use App\Core\Modules\Words\Dto\ImportWordTranslationsResultDto;
use App\Core\Modules\Words\Tasks\ImportWordTranslationsTask;
use App\Infrastructure\Learning\Sources\Contracts\WordTranslationsSourceContract;
use App\Infrastructure\Support\Exceptions\MissingResourceException;
use Illuminate\Support\Facades\DB;
use Throwable;

final class ImportWordTranslationsAction extends Action
{
    public function __construct(
        private readonly ImportWordTranslationsTask $importTask,
        private readonly WordTranslationsSourceContract $translationsSource,
    ){}

    /**
     * @param string $resourceName имя ресурса
     * @param bool $fresh очистить таблицу переводов
     * @throws Throwable|MissingResourceException
     */
    public function run(string $resourceName, bool $fresh = false): ImportWordTranslationsResultDto
    {
        if ($fresh) {
            $this->truncateTable();
        }

        $words = $this->translationsSource->get($resourceName);

        return $this->importTask->run($words);
    }

    private function truncateTable(): void
    {
        DB::statement('TRUNCATE word_translations CASCADE');
    }
}