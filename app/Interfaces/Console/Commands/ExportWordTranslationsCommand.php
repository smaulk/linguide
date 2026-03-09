<?php
declare(strict_types=1);

namespace App\Interfaces\Console\Commands;

use App\Core\Modules\Words\Actions\ExportWordTranslationsAction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

final class ExportWordTranslationsCommand extends Command
{
    protected $signature = 'learning:translations:export  
                            {resourceName : Name of resource}';

    protected $description = 'Export words translations';

    public function handle(ExportWordTranslationsAction $exportAction): int
    {
        $resourceName = $this->argument('resourceName');

        try {
            $exportedCount = $exportAction->run($resourceName);
        } catch (Throwable $e) {
            Log::error('Error exporting words: ' . $e->getMessage());
            $this->error('Export words failed');

            return self::FAILURE;
        }

        $this->info("Exported: $exportedCount");

        return self::SUCCESS;
    }
}