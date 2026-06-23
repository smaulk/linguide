<?php
declare(strict_types=1);

namespace App\Infrastructure\Modules\Term\Contracts;

use App\Core\Modules\Term\Dto\StoreTranslationTermDto;
use App\Infrastructure\Common\Exceptions\MissingResourceException;

interface TranslationsSourceContract
{
    /**
     * @param string $name имя ресурса
     * @return iterable<StoreTranslationTermDto>
     * @throws MissingResourceException
     */
    public function get(string $name): iterable;
}