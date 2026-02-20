<?php
declare(strict_types=1);

namespace App\Core\Modules\AiConversation\Models;

use App\Core\Common\Parents\Model;
use App\Core\Modules\AiConversation\Enums\AiConversationMode;
use App\Core\Modules\User\Models\User;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $user_id
 * @property AiConversationMode $mode
 * @property ?DateTimeInterface $created_at
 * @property ?DateTimeInterface $updated_at
 * @property ?DateTimeInterface $deleted_at
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