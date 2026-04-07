<?php
declare(strict_types=1);

namespace App\Infrastructure\Common\Data\Seeders;

use App\Infrastructure\Common\Parents\Seeder;
use App\Infrastructure\Modules\App\Data\Seeders\MetadataSeeder;
use App\Infrastructure\Modules\Dictionary\Data\Seeders\DictionarySeeder;

final class AppDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call($this->baseSeeders());

        if(app()->environment('local', 'testing')) {
            $this->call($this->devSeeders());
        }
    }

    /**
     * @return list<class-string<Seeder>>
     */
    private function baseSeeders(): array
    {
        return [
            DictionarySeeder::class,
            MetadataSeeder::class,
        ];
    }

    /**
     * @return list<class-string<Seeder>>
     */
    private function devSeeders(): array
    {
        return [
            // Dev сидеры
        ];
    }
}