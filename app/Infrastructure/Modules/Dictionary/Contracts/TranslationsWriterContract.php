<?php
declare(strict_types=1);

namespace App\Infrastructure\Modules\Dictionary\Contracts;

use App\Core\Modules\Dictionary\Dto\TermTranslationDatasetDto;

interface TranslationsWriterContract
{
    /**
     * @param string $resourceName имя ресурса
     * @param TermTranslationDatasetDto[] $terms
     */
    public function write(string $resourceName, array $terms): void;
}