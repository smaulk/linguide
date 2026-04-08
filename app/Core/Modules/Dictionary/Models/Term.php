<?php
declare(strict_types=1);

namespace App\Core\Modules\Dictionary\Models;

use App\Core\Common\Parents\Model;
use App\Core\Modules\Dictionary\Enums\TermType;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $text
 * @property TermType $type
 * @property bool $is_verified
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property-read Collection<int, TermVariant> $variants
 */
final class Term extends Model
{
    protected $table = 'terms';

    protected $casts = [
        'type'        => TermType::class,
        'is_verified' => 'boolean',
    ];

    /**
     * @return HasMany<TermVariant, $this>
     */
    public function variants(): HasMany
    {
        return $this->hasMany(TermVariant::class, 'term_id', 'id');
    }
}