<?php
declare(strict_types=1);

use App\Core\Modules\Term\Actions\SendReviewRemindersAction;
use Illuminate\Console\Scheduling\Schedule;

/** @var Schedule $schedule */

$schedule->call(fn() => app(SendReviewRemindersAction::class)->run())
    ->hourly();