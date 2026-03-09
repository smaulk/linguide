<?php
declare(strict_types=1);

namespace App\Interfaces\Console\Commands;

use App\Core\Modules\Words\Actions\ImportWordsAction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

final class ImportWordsCommand extends Command
{
    protected $signature = 'learning:words:import
                            {resourceName : Name of resource}
                            {--force : Truncate words table before import}';

    protected $description = 'Imports words from resource into a database';

    public function handle(ImportWordsAction $importAction): int
    {
        $resourceName = $this->argument('resourceName');
        $force = $this->option('force') === true;

        if ($force) {
            if (!$this->confirm('This will truncate the words table. Continue?')) {
                return self::FAILURE;
            }
        }

        try {
            $importCount = $importAction->run($resourceName, $force);
        } catch (Throwable $e) {
            Log::error('Error importing words from resource "' . $resourceName . '": ' . $e->getMessage());
            $this->error('Importing words from resource "' . $resourceName . '" failed');

            return self::FAILURE;
        }

        $this->info("Imported: {$importCount}");

        return self::SUCCESS;
    }
}