<?php
declare(strict_types=1);

namespace App\Core\Modules\Words\Actions;

use App\Core\Common\Parents\Action;
use App\Core\Modules\Words\Tasks\ImportWordsTask;
use App\Infrastructure\Learning\Sources\Contracts\WordSourceContract;
use App\Infrastructure\Support\Exceptions\MissingResourceException;
use Illuminate\Support\Facades\DB;

final class ImportWordsAction extends Action
{
    public function __construct(
        private readonly ImportWordsTask $importTask,
        private readonly WordSourceContract $wordSource
    ){}

    /**
     * @param string $resourceName имя ресурса
     * @param bool $force очистить таблицу слов
     * @return int количество импортированных слов
     * @throws MissingResourceException
     */
    public function run(string $resourceName, bool $force): int
    {
        if ($force) {
            $this->truncateTable();
        }

        $rawWords = $this->wordSource->get($resourceName);

        return $this->importTask->run($rawWords);
    }

    private function truncateTable(): void
    {
        DB::statement('TRUNCATE words CASCADE');
    }
}