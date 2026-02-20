<?php
declare(strict_types=1);

namespace App\Core\Modules\AiConversation\Models;

use App\Core\Common\Parents\Model;
use App\Core\Modules\AiConversation\Enums\AiMessageRole;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $conversation_id
 * @property ?int $telegram_message_id
 * @property AiMessageRole $role
 * @property string $content
 * @property ?array<string, mixed> $meta
 * @property ?DateTimeInterface $created_at
 * @property ?DateTimeInterface $updated_at
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