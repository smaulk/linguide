<?php
declare(strict_types=1);

namespace App\Infrastructure\Modules\Dictionary\Data\Seeders;

use App\Core\Modules\Dictionary\Actions\InitializeDictionaryAction;
use App\Core\Modules\Dictionary\Dto\InitializeDictionaryResultDto;
use App\Infrastructure\Common\Parents\Seeder;
use App\Infrastructure\Modules\Dictionary\Resolvers\DictionaryConfigResolver;
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
        if($config === null) {
            return;
        }

        try {
            $result = $action->run($config);
        } catch (Throwable $e) {
            Log::error('Error initializing dictionary.', ['exception' => $e]);
            $this->command->error('Initializing dictionary failed');
            return;
        }

        if($result === null) {
            return;
        }

        $this->command->info($this->formatResult($result));
    }

    private function formatResult(InitializeDictionaryResultDto $result): string
    {
        $lines = [
            'Imported',
            "Words: {$result->wordsCount}",
        ];

        if ($result->translationsResult !== null) {
            $lines[] = "Translations for words: {$result->translationsResult->words}";
            $lines[] = "Translations: {$result->translationsResult->translations}";
            $lines[] = "Examples: {$result->translationsResult->examples}";
        }

        return implode("\n", $lines);
    }
}