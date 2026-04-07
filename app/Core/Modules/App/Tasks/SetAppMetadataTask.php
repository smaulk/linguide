<?php
declare(strict_types=1);

namespace App\Core\Modules\App\Tasks;

use App\Core\Common\Parents\Task;
use Illuminate\Support\Facades\DB;

final class SetAppMetadataTask extends Task
{
    /**
     * @param string $key ключ
     * @param string $value значение
     * @return void
     */
    public function run(string $key, string $value): void
    {
        $now = now();

        DB::table('app_metadata')->upsert(
            [[
                'key'        => $key,
                'value'      => $value,
                'created_at' => $now,
                'updated_at' => $now,
            ]],
            ['key'],
            ['value', 'updated_at']
        );
    }
}