<?php
declare(strict_types=1);

namespace App\Core\Modules\Dictionary\Actions;

use App\Core\Common\Parents\Action;
use App\Core\Modules\Dictionary\Dto\ImportWordTranslationsResultDto;
use App\Core\Modules\Dictionary\SubActions\ImportWordTranslationsSubAction;
use App\Infrastructure\Common\Exceptions\MissingResourceException;
use Illuminate\Support\Facades\DB;
use Throwable;

final class ImportWordTranslationsAction extends Action
{
    public function __construct(private readonly ImportWordTranslationsSubAction $importSubAction){}

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

        return $this->importSubAction->run($resourceName);
    }

    private function truncateTable(): void
    {
        DB::statement('TRUNCATE word_translations RESTART IDENTITY CASCADE');
    }
}