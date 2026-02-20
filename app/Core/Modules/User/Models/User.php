<?php
declare(strict_types=1);

namespace App\Core\Modules\User\Models;

use App\Core\Common\Parents\Model;
use App\Core\Modules\AiConversation\Models\AiConversation;
use DateTimeInterface;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name
 * @property ?DateTimeInterface $created_at
 * @property ?DateTimeInterface $updated_at
 * @property ?DateTimeInterface $deleted_at
 */
final class User extends Model implements AuthenticatableContract
{
    use Authenticatable, SoftDeletes;

    protected $fillable = [
        'mode',
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