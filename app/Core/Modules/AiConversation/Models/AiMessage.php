<?php
declare(strict_types=1);

namespace App\Core\Modules\AiConversation\Models;

use App\Core\Common\Parents\Model;
use App\Core\Modules\AiConversation\Enums\AiMessageRole;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $conversation_id
 * @property ?int $telegram_message_id
 * @property AiMessageRole $role
 * @property string $content
 * @property ?array<string, mixed> $meta
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property-read AiConversation $conversation
 */
final class AiMessage extends Model
{
    protected $casts = [
        'role' => AiMessageRole::class,
        'meta' => 'json',
    ];

    /**
     * @return BelongsTo<AiConversation, $this>
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(AiConversation::class, 'conversation_id', 'id');
    }
}