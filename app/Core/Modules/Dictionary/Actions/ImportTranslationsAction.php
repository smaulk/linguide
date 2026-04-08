<?php
declare(strict_types=1);

namespace App\Core\Modules\Dictionary\Actions;

use App\Core\Common\Parents\Action;
use App\Core\Modules\Dictionary\Dto\ImportTranslationsResultDto;
use App\Core\Modules\Dictionary\SubActions\ImportTranslationsSubAction;
use App\Infrastructure\Common\Exceptions\MissingResourceException;
use Illuminate\Support\Facades\DB;
use Throwable;

final class ImportTranslationsAction extends Action
{
    public function __construct(private readonly ImportTranslationsSubAction $importSubAction){}

    /**
     * @param string $resourceName имя ресурса
     * @param bool $fresh очистить таблицу переводов
     * @throws Throwable|MissingResourceException
     */
    public function run(string $resourceName, bool $fresh = false): ImportTranslationsResultDto
    {
        if ($fresh) {
            $this->truncateTable();
        }

        return $this->importSubAction->run($resourceName);
    }

    private function truncateTable(): void
    {
        DB::statement('TRUNCATE translations RESTART IDENTITY CASCADE');
    }
}