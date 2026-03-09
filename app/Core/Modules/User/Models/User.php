<?php
declare(strict_types=1);

namespace App\Core\Modules\User\Models;

use App\Core\Common\Parents\Model;
use App\Core\Modules\AiConversation\Models\AiConversation;
use App\Core\Modules\User\Enums\LanguageLevel;
use DateTimeInterface;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Collection;

/**
 * @property int $id
 * @property string $name
 * @property ?LanguageLevel $level
 * @property ?DateTimeInterface $created_at
 * @property ?DateTimeInterface $updated_at
 * @property ?DateTimeInterface $deleted_at
 *
 * @property-read Collection<int, UserIdentity> $identities
 * @property-read Collection<int, AiConversation> $aiConversations
 */
final class User extends Model implements AuthenticatableContract
{
    use Authenticatable, SoftDeletes;

    protected $casts = [
        'level' => LanguageLevel::class,
    ];

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
}