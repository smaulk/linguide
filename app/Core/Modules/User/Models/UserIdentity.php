<?php
declare(strict_types=1);

namespace App\Core\Modules\User\Models;

use App\Core\Common\Base\Model;
use App\Core\Modules\User\Enums\UserProviderType;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property UserProviderType $provider
 * @property ?string $provider_user_id
 * @property ?string $email
 * @property ?string $password
 * @property ?string $email_verified_at
 * @property DateTimeInterface $created_at
 * @property DateTimeInterface $updated_at
 */
final class UserIdentity extends Model
{
    public $fillable = [
        'user_id',
        'provider',
        'provider_user_id',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'provider' => UserProviderType::class,
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}