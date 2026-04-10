<?php
declare(strict_types=1);

namespace App\Infrastructure\Modules\Term\Contracts;

use App\Core\Modules\Term\Dto\TermTranslationDatasetDto;
use App\Infrastructure\Common\Exceptions\MissingResourceException;

interface TranslationsSourceContract
{
    /**
     * @param string $name имя ресурса
     * @return iterable<TermTranslationDatasetDto>
     * @throws MissingResourceException
     */
    public function get(string $name): iterable;
}