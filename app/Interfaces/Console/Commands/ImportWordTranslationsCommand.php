<?php
declare(strict_types=1);

namespace App\Interfaces\Console\Commands;

use App\Core\Modules\Words\Actions\ImportWordTranslationsAction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

final class ImportWordTranslationsCommand extends Command
{
    protected $signature = 'learning:translations:import  
                            {resourceName : Name of resource}';

    protected $description = 'Import word translations';

    public function handle(ImportWordTranslationsAction $importAction): int
    {
        $resourceName = $this->argument('resourceName');

        try {
            $importResult = $importAction->run($resourceName);
        } catch (Throwable $e) {
            Log::error('Error importing word translations from resource "' . $resourceName . '": ' . $e->getMessage());
            $this->error('Importing word translations from resource "' . $resourceName . '" failed');

            return self::FAILURE;
        }

        $this->info("Words: {$importResult->words}");
        $this->info("Translations: {$importResult->translations}");
        $this->info("Examples: {$importResult->examples}");


        return self::SUCCESS;
    }
}