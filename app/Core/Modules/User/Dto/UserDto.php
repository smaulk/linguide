<?php
declare(strict_types=1);

namespace App\Core\Modules\User\Dto;

use App\Core\Common\Parents\Dto;

final readonly class UserDto extends Dto
{
    public function __construct(
        public int $id,
        public string $name,
    ){}
}