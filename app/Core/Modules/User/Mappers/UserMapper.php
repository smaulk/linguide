<?php
declare(strict_types=1);

namespace App\Core\Modules\User\Mappers;

use App\Core\Modules\User\Dto\UserDto;
use App\Core\Modules\User\Dto\UserSettingsDto;
use App\Core\Modules\User\Models\User;
use App\Core\Modules\User\Vo\UtcOffset;
use App\Core\Modules\User\Vo\ReviewLimit;
use LogicException;

final class UserMapper
{
    /**
     * @throws LogicException
     */
    public function mapUserModelToDto(User $user): UserDto
    {
        $settings = $user->settingsOrFail();

        $reviewLimit = ReviewLimit::fromInt($settings->review_limit);
        $utcOffset = $settings->utc_offset !== null
            ? UtcOffset::fromInt($settings->utc_offset)
            : null;

        return new UserDto(
            id: $user->id,
            name: $user->name,
            status: $user->status,
            settings: new UserSettingsDto(
                level: $settings->level,
                utcOffset: $utcOffset,
                reviewLimit: $reviewLimit,
            ),
        );
    }
}