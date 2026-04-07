<?php
declare(strict_types=1);

namespace App\Core\Modules\Dictionary\Dto;

use App\Core\Common\Parents\Dto;
use Illuminate\Support\Carbon;

final readonly class WordProgressDto extends Dto
{
    public function __construct(
        public int $id,
        public int $repetitions,
        public int $interval,
        public float $ease_factor,
        public Carbon $due_at,
        public ?Carbon $last_reviewed_at,
        public ?Carbon $created_at,
        public WordDto $word,
    ){}
}