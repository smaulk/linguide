<?php
declare(strict_types=1);

namespace App\Core\Modules\Dictionary\Actions;

use App\Core\Common\Parents\Action;
use App\Core\Modules\Dictionary\SubActions\ImportWordsSubAction;
use App\Infrastructure\Common\Exceptions\MissingResourceException;
use Illuminate\Support\Facades\DB;

final class ImportWordsAction extends Action
{
    public function __construct(private readonly ImportWordsSubAction $importSubAction){}

    /**
     * @param string $resourceName имя ресурса
     * @param bool $fresh очистить таблицу слов
     * @return int количество импортированных слов
     * @throws MissingResourceException
     */
    public function run(string $resourceName, bool $fresh = false): int
    {
        if ($fresh) {
            $this->truncateTable();
        }

        return $this->importSubAction->run($resourceName);
    }

    private function truncateTable(): void
    {
        DB::statement('TRUNCATE words RESTART IDENTITY CASCADE');
    }
}