<?php
declare(strict_types=1);

namespace App\Core\Modules\User\Dto;

use App\Core\Common\Parents\Dto;
use App\Core\Modules\User\Enums\LanguageLevel;
use App\Core\Modules\User\Vo\UtcOffset;
use App\Core\Modules\User\Vo\WordsReviewLimit;

final readonly class UserSettingsDto extends Dto
{
    public function __construct(
        public ?LanguageLevel $level,
        public ?UtcOffset $utcOffset,
        public WordsReviewLimit $wordsReviewLimit,
    ){}
}