<?php
declare(strict_types=1);

namespace App\Infrastructure\Modules\Term\Data\Seeders;

use App\Core\Modules\Term\Actions\InitializeDictionaryAction;
use App\Core\Modules\Term\Dto\InitializeDictionaryResultDto;
use App\Infrastructure\Common\Parents\Seeder;
use App\Infrastructure\Modules\Term\Resolvers\DictionaryConfigResolver;
use Illuminate\Support\Facades\Log;
use Throwable;

final class DictionarySeeder extends Seeder
{
    /**
     * @throws Throwable
     */
    public function run(DictionaryConfigResolver $configResolver, InitializeDictionaryAction $action): void
    {
        $config = $configResolver->resolve();
        if ($config === null) {
            return;
        }

        try {
            $result = $action->run($config);
        } catch (Throwable $e) {
            Log::error('Error initializing dictionary.', ['exception' => $e]);
            $this->command->error('Initializing dictionary failed');
            return;
        }

        if ($result === null) {
            return;
        }

        $this->command->info($this->formatResult($result));
    }

    private function formatResult(InitializeDictionaryResultDto $result): string
    {
        $lines = [
            'Imported',
            "Terms: {$result->termsResult->terms}",
            "Variants: {$result->termsResult->variants}",
        ];

        if ($result->translationsResult !== null) {
            $lines[] = "Translations for term variants: {$result->translationsResult->variants}";
            $lines[] = "Translations: {$result->translationsResult->translations}";
            $lines[] = "Examples: {$result->translationsResult->examples}";
        }

        return implode("\n", $lines);
    }
}