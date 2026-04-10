<?php
declare(strict_types=1);

namespace App\Interfaces\Console\Commands;

use App\Core\Modules\Term\Actions\ImportTermsAction;
use App\Core\Modules\Term\Dto\ImportTermsResultDto;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

final class ImportTermsCommand extends Command
{
    protected $signature = 'learning:terms:import
                            {resourceName : Name of resource}
                            {--fresh : Truncate terms table before import}';

    protected $description = 'Imports terms from resource into a database';

    public function handle(ImportTermsAction $importAction): int
    {
        $resourceName = $this->argument('resourceName');
        $fresh = $this->option('fresh') === true;

        if ($fresh) {
            if (!$this->confirm('This will truncate the terms table. Continue?')) {
                return self::FAILURE;
            }
        }

        try {
            $importResult = $importAction->run($resourceName, $fresh);
        } catch (Throwable $e) {
            Log::error('Error importing terms from resource "' . $resourceName . '".', ['exception' => $e]);
            $this->error('Importing terms from resource "' . $resourceName . '" failed');

            return self::FAILURE;
        }

        $this->info($this->formatResult($importResult));

        return self::SUCCESS;
    }

    private function formatResult(ImportTermsResultDto $result): string
    {
        $lines = [
            "Imported",
            "Terms: {$result->terms}",
            "Variants: {$result->variants}",
        ];

        return implode("\n", $lines);
    }
}