<?php
declare(strict_types=1);

namespace App\Core\Modules\User\Models;

use App\Core\Common\Parents\Model;
use App\Core\Modules\User\Enums\UserProviderType;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property UserProviderType $provider
 * @property ?string $provider_user_id
 * @property ?string $email
 * @property ?string $password
 * @property ?string $email_verified_at
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property ?Carbon $deleted_at
 *
 * @property-read User $user
 */
final class UserIdentity extends Model
{
    use SoftDeletes;

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'provider'          => UserProviderType::class,
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}