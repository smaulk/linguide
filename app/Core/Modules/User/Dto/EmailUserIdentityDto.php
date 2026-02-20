<?php
declare(strict_types=1);

namespace App\Core\Modules\User\Dto;

use App\Core\Common\Parents\Dto;

final readonly class EmailUserIdentityDto extends Dto
{
    public function __construct(
        public string $email,
        public string $password,
    ){}
}