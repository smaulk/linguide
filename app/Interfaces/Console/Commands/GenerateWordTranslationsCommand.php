<?php
declare(strict_types=1);

namespace App\Interfaces\Console\Commands;

use App\Core\Modules\Words\Actions\GenerateWordTranslationsAction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;


final class GenerateWordTranslationsCommand extends Command
{
    protected $signature = 'learning:translations:generate  
                            {resourceName : Name of resource}
                            {--empty : Generate translations only for words without translations}';

    protected $description = 'Generate words translations';

    public function handle(GenerateWordTranslationsAction $generateAction): int
    {
        $resourceName = $this->argument('resourceName');
        $isOnlyEmpty = $this->option('empty') === true;

        try {
            $generatedCount = $generateAction->run($resourceName, $isOnlyEmpty);
        } catch (Throwable $e) {
            Log::error('Error generate word translations: ' . $e->getMessage());
            $this->error('Generate word translations failed');

            return self::FAILURE;
        }

        $this->info("Generated for words: $generatedCount");

        return self::SUCCESS;
    }
}