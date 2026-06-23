<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Actions;

use App\Core\Common\Parents\Action;
use App\Core\Modules\Ai\Exceptions\AiUnavailableException;
use App\Core\Modules\Term\Tasks\GenerateTranslationsTask;
use App\Infrastructure\Modules\Term\Contracts\TranslationsWriterContract;
use Illuminate\Contracts\Container\BindingResolutionException;

final class GenerateAndWriteTranslationsAction extends Action
{
    private const int WRITE_BATCH_SIZE = 10;

    public function __construct(
        private readonly GenerateTranslationsTask $generateTask,
        private readonly TranslationsWriterContract $translationsWriter,
    ){}

    /**
     * @throws BindingResolutionException
     * @throws AiUnavailableException
     */
    public function run(string $resourceName, bool $isOnlyEmpty): int
    {
        $generatedCount = 0;
        $translations = $this->generateTask->run($isOnlyEmpty);

        foreach (chunk_iterable($translations, self::WRITE_BATCH_SIZE) as $terms) {
            $generatedCount += count($terms);
            $this->translationsWriter->write($resourceName, $terms);
        }

        return $generatedCount;
    }
}