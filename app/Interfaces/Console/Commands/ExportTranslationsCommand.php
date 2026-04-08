<?php
declare(strict_types=1);

namespace App\Interfaces\Console\Commands;

use App\Core\Modules\Dictionary\Actions\ExportTranslationsAction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

final class ExportTranslationsCommand extends Command
{
    protected $signature = 'learning:translations:export  
                            {resourceName : Name of resource}';

    protected $description = 'Export terms translations';

    public function handle(ExportTranslationsAction $exportAction): int
    {
        $resourceName = $this->argument('resourceName');

        try {
            $exportedCount = $exportAction->run($resourceName);
        } catch (Throwable $e) {
            Log::error('Error exporting terms translations.', ['exception' => $e]);
            $this->error('Export terms translations failed');

            return self::FAILURE;
        }

        $this->info("Exported translations\nTerm variants: $exportedCount");

        return self::SUCCESS;
    }
}