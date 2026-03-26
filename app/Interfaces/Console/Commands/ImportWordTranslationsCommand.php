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
                            {resourceName : Name of resource}
                            {--fresh : Truncate translations table before import}';

    protected $description = 'Import word translations from resource into a database';

    public function handle(ImportWordTranslationsAction $importAction): int
    {
        $resourceName = $this->argument('resourceName');
        $fresh = $this->option('fresh') === true;

        if ($fresh) {
            if (!$this->confirm('This will truncate the translations table. Continue?')) {
                return self::FAILURE;
            }
        }

        try {
            $importResult = $importAction->run($resourceName, $fresh);
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