<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Infrastructure\Common\Data\Seeders\AppDatabaseSeeder;
use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AppDatabaseSeeder::class,
        ]);
    }
}