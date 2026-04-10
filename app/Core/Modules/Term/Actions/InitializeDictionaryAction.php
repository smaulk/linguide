<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Actions;

use App\Core\Common\Parents\Action;
use App\Core\Modules\App\Tasks\GetAppMetadataTask;
use App\Core\Modules\App\Tasks\SetAppMetadataTask;
use App\Core\Modules\Term\Dto\DictionaryImportConfigDto;
use App\Core\Modules\Term\Dto\InitializeDictionaryResultDto;
use App\Core\Modules\Term\SubActions\ImportTermsSubAction;
use App\Core\Modules\Term\SubActions\ImportTranslationsSubAction;
use App\Infrastructure\Common\Exceptions\MissingResourceException;
use Illuminate\Support\Facades\DB;
use Throwable;

final class InitializeDictionaryAction extends Action
{
    private const string METADATA_KEY = 'dictionary_initialized';
    private const string TRUE         = '1';

    public function __construct(
        private readonly ImportTermsSubAction $importTermsSubAction,
        private readonly ImportTranslationsSubAction $importTranslationsSubAction,
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

            $importTermsResult = $this->importTermsSubAction->run($dto->termsResource);
            $importTranslationsResult = $dto->translationsResource !== null
                ? $this->importTranslationsSubAction->run($dto->translationsResource)
                : null;

            $this->markAsInitialized();

            return new InitializeDictionaryResultDto(
                termsResult: $importTermsResult,
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
        DB::statement('TRUNCATE terms RESTART IDENTITY CASCADE');
    }
}