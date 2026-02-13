<?php
declare(strict_types=1);

namespace App\Core\Modules\User\Models;

use App\Core\Common\Base\Model;
use DateTimeInterface;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

/**
 * @property int $id
 * @property string $name
 * @property DateTimeInterface $created_at
 * @property DateTimeInterface $updated_at
 */
final class User extends Model implements AuthenticatableContract
{
    use Authenticatable;

    public $fillable = [
        'name',
    ];

    public function identities(): HasMany
    {
        return $this->hasMany(UserIdentity::class, 'user_id', 'id');
    }
}