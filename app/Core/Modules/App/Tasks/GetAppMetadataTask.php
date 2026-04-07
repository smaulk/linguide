<?php
declare(strict_types=1);

namespace App\Core\Modules\App\Tasks;

use App\Core\Common\Parents\Task;
use Illuminate\Support\Facades\DB;

final class GetAppMetadataTask extends Task
{
    /**
     * @param string $key ключ
     * @return string|null значение, null - запись не найдена
     */
    public function run(string $key): ?string
    {
        return DB::table('app_metadata')
            ->where('key', $key)
            ->value('value');
    }
}