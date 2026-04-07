<?php
declare(strict_types=1);

namespace App\Core\Modules\Dictionary\Actions;

use App\Core\Common\Parents\Action;
use App\Core\Modules\App\Tasks\GetAppMetadataTask;
use App\Core\Modules\App\Tasks\SetAppMetadataTask;
use App\Core\Modules\Dictionary\Dto\DictionaryImportConfigDto;
use App\Core\Modules\Dictionary\Dto\InitializeDictionaryResultDto;
use App\Core\Modules\Dictionary\SubActions\ImportWordsSubAction;
use App\Core\Modules\Dictionary\SubActions\ImportWordTranslationsSubAction;
use App\Infrastructure\Common\Exceptions\MissingResourceException;
use Illuminate\Support\Facades\DB;
use Throwable;

final class InitializeDictionaryAction extends Action
{
    private const string METADATA_KEY = 'dictionary_initialized';
    private const string TRUE         = '1';

    public function __construct(
        private readonly ImportWordsSubAction $importWordsSubAction,
        private readonly ImportWordTranslationsSubAction $importTranslationsSubAction,
        private readonly GetAppMetadataTask $getMetadataTask,
        private readonly SetAppMetadataTask $setMetadataTask,
    ){}

    /**
     * @throws MissingResourceException
     * @throws Throwable
     */
    public function run(DictionaryImportConfigDto $dto): ?InitializeDictionaryResultDto
    {
        if ($this->isAlreadyInitialized()) {
            return null;
        }

        return DB::transaction(function () use ($dto) {
            $this->truncateDictionary();

            $importWordsCount = $this->importWordsSubAction->run($dto->wordsResource);
            $importTranslationsResult = $dto->translationsResource !== null
                ? $this->importTranslationsSubAction->run($dto->translationsResource)
                : null;

            $this->markAsInitialized();

            return new InitializeDictionaryResultDto(
                wordsCount: $importWordsCount,
                translationsResult: $importTranslationsResult,
            );
        });
    }

    private function isAlreadyInitialized(): bool
    {
        return $this->getMetadataTask->run(self::METADATA_KEY) === self::TRUE;
    }

    private function markAsInitialized(): void
    {
        $this->setMetadataTask->run(self::METADATA_KEY, self::TRUE);
    }

    private function truncateDictionary(): void
    {
        DB::statement('TRUNCATE words RESTART IDENTITY CASCADE');
    }
}