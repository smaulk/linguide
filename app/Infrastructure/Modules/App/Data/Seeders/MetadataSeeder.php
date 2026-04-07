<?php
declare(strict_types=1);

namespace App\Infrastructure\Modules\App\Data\Seeders;

use App\Core\Modules\App\Classes\AccessManager;
use App\Infrastructure\Common\Parents\Seeder;

final class MetadataSeeder extends Seeder
{
    public function run(AccessManager $accessManager): void
    {
        // Если ключ уже установлен
        if($accessManager->getAccessControl() !== null){
            return;
        }

        $accessManager->setAccessControl(true);
        $accessManager->setAccessCode('');
    }
}