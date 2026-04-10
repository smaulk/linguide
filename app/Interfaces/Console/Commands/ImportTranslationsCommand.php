<?php
declare(strict_types=1);

namespace App\Interfaces\Console\Commands;

use App\Core\Modules\Term\Actions\ImportTranslationsAction;
use App\Core\Modules\Term\Dto\ImportTranslationsResultDto;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

final class ImportTranslationsCommand extends Command
{
    protected $signature = 'learning:translations:import  
                            {resourceName : Name of resource}
                            {--fresh : Truncate translations table before import}';

    protected $description = 'Import term translations from resource into a database';

    public function handle(ImportTranslationsAction $importAction): int
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
            Log::error(
                'Error importing term translations from resource "' . $resourceName . '".',
                ['exception' => $e]
            );
            $this->error('Importing term translations from resource "' . $resourceName . '" failed');

            return self::FAILURE;
        }

        $this->info($this->formatResult($importResult));

        return self::SUCCESS;
    }

    private function formatResult(ImportTranslationsResultDto $result): string
    {
        $lines = [
            "Imported",
            "Translations for term variants: {$result->variants}",
            "Translations: {$result->translations}",
            "Examples: {$result->examples}",
        ];

        return implode("\n", $lines);
    }
}