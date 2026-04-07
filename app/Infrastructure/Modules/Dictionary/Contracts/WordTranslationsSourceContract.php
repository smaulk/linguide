<?php
declare(strict_types=1);

namespace App\Infrastructure\Modules\Dictionary\Contracts;

use App\Core\Modules\Dictionary\Dto\WordDatasetDto;
use App\Infrastructure\Common\Exceptions\MissingResourceException;

interface WordTranslationsSourceContract
{
    /**
     * @param string $name имя ресурса
     * @return iterable<WordDatasetDto>
     * @throws MissingResourceException
     */
    public function get(string $name): iterable;
}