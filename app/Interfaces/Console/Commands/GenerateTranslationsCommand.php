<?php
declare(strict_types=1);

namespace App\Interfaces\Console\Commands;

use App\Core\Modules\Term\Actions\GenerateTranslationsAction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

final class GenerateTranslationsCommand extends Command
{
    protected $signature = 'learning:translations:generate  
                            {resourceName : Name of resource}
                            {--empty : Generate translations only for term variants without translations}';

    protected $description = 'Generate term translations';

    public function handle(GenerateTranslationsAction $generateAction): int
    {
        $resourceName = $this->argument('resourceName');
        $isOnlyEmpty = $this->option('empty') === true;

        try {
            $generatedCount = $generateAction->run($resourceName, $isOnlyEmpty);
        } catch (Throwable $e) {
            Log::error('Error generate translations.', ['exception' => $e]);
            $this->error('Generate translations failed');

            return self::FAILURE;
        }

        $this->info("Generated for term variants: $generatedCount");

        return self::SUCCESS;
    }
}