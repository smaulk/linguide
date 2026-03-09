<?php
declare(strict_types=1);

namespace App\Core\Modules\Words\Dto;

use App\Core\Common\Parents\Dto;

final readonly class RawWordDto extends Dto
{
    public function __construct(
        public string $text,
        public string $pos,
        public string $level,
    ){}
}