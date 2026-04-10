<?php
declare(strict_types=1);

namespace App\Infrastructure\Modules\Term\Contracts;

use App\Core\Modules\Term\Dto\TermTranslationDatasetDto;

interface TranslationsWriterContract
{
    /**
     * @param string $resourceName имя ресурса
     * @param TermTranslationDatasetDto[] $terms
     */
    public function write(string $resourceName, array $terms): void;
}