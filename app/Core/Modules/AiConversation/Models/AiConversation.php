<?php
declare(strict_types=1);

namespace App\Core\Modules\AiConversation\Models;

use App\Core\Common\Parents\Model;
use App\Core\Modules\AiConversation\Enums\AiConversationMode;
use App\Core\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property AiConversationMode $mode
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property ?Carbon $deleted_at
 *
 * @property-read User $user
 * @property-read Collection<int, AiMessage> $messages
 */
final class AiConversation extends Model
{
    use SoftDeletes;

    protected $casts = [
        'mode' => AiConversationMode::class,
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * @return HasMany<AiMessage, $this>
     */
    public function messages(): HasMany
    {
        return $this->hasMany(AiMessage::class, 'conversation_id', 'id');
    }
}