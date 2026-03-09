<?php
declare(strict_types=1);

namespace App\Infrastructure\Learning\Writers\Contracts;

use App\Core\Modules\Words\Dto\WordDto;

interface WordTranslationsWriterContract
{
    /**
     * @param string $resourceName имя ресурса
     * @param WordDto[] $words
     */
    public function write(string $resourceName, array $words): void;
}