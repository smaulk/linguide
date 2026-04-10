<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Actions;

use App\Core\Common\Parents\Action;
use App\Core\Modules\Term\Dto\ImportTermsResultDto;
use App\Core\Modules\Term\SubActions\ImportTermsSubAction;
use App\Infrastructure\Common\Exceptions\MissingResourceException;
use Illuminate\Support\Facades\DB;
use Throwable;

final class ImportTermsAction extends Action
{
    public function __construct(private readonly ImportTermsSubAction $importSubAction){}

    /**
     * @param string $resourceName имя ресурса
     * @param bool $fresh очистить таблицу терминов
     * @return ImportTermsResultDto
     * @throws Throwable
     * @throws MissingResourceException
     */
    public function run(string $resourceName, bool $fresh = false): ImportTermsResultDto
    {
        if ($fresh) {
            $this->truncateTable();
        }

        return $this->importSubAction->run($resourceName);
    }

    private function truncateTable(): void
    {
        DB::statement('TRUNCATE terms RESTART IDENTITY CASCADE');
    }
}