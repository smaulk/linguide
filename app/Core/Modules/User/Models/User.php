<?php
declare(strict_types=1);

namespace App\Core\Modules\User\Models;

use App\Core\Common\Parents\Model;
use App\Core\Modules\AiConversation\Models\AiConversation;
use App\Core\Modules\Dictionary\Models\LearningProgress;
use App\Core\Modules\User\Enums\UserStatus;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use LogicException;

/**
 * @property int $id
 * @property string $name
 * @property UserStatus $status
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property ?Carbon $deleted_at
 *
 * @property-read ?UserSetting $settings
 * @property-read Collection<int, UserIdentity> $identities
 * @property-read Collection<int, AiConversation> $aiConversations
 * @property-read Collection<int, LearningProgress> $learningProgress
 */
final class User extends Model implements AuthenticatableContract
{
    use Authenticatable, SoftDeletes;

    protected $casts = [
        'status' => UserStatus::class,
    ];

    /**
     * @return HasOne<UserSetting, $this>
     */
    public function settings(): HasOne
    {
        return $this->hasOne(UserSetting::class, 'user_id', 'id');
    }

    /**
     * @return HasMany<UserIdentity, $this>
     */
    public function identities(): HasMany
    {
        return $this->hasMany(UserIdentity::class, 'user_id', 'id');
    }

    /**
     * @return HasMany<AiConversation, $this>
     */
    public function aiConversations(): HasMany
    {
        return $this->hasMany(AiConversation::class, 'conversation_id', 'id');
    }

    /**
     * @return HasMany<LearningProgress, $this>
     */
    public function learningProgress(): HasMany
    {
        return $this->hasMany(LearningProgress::class, 'user_id', 'id');
    }

    /**
     * @throws LogicException
     */
    public function settingsOrFail(): UserSetting
    {
        $settings = $this->settings;

        if ($settings === null) {
            throw new LogicException('User settings not found');
        }

        return $settings;
    }
}