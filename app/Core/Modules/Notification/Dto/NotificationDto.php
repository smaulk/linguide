<?php
declare(strict_types=1);

namespace App\Core\Modules\Notification\Dto;

use App\Core\Common\Parents\Dto;

final readonly class NotificationDto extends Dto
{
    public function __construct(
        public string $text,
    ) {}
}