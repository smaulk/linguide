<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Enums;

use App\Core\Common\Concerns\BaseEnum;

enum ReviewMode: string
{
    use BaseEnum;

    case EnglishToRussian = 'en_to_ru';
    case RussianToEnglish = 'ru_to_en';
}