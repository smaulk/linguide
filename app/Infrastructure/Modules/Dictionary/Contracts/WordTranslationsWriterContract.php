<?php
declare(strict_types=1);

namespace App\Infrastructure\Modules\Dictionary\Contracts;

use App\Core\Modules\Dictionary\Dto\WordDatasetDto;

interface WordTranslationsWriterContract
{
    /**
     * @param string $resourceName имя ресурса
     * @param WordDatasetDto[] $words
     */
    public function write(string $resourceName, array $words): void;
}