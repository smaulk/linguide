<?php

declare(strict_types=1);

namespace App\Core\Modules\Term\Tests\Feature;

use App\Core\Modules\Term\Actions\GetUserReviewPeriodStatisticsAction;
use App\Core\Modules\Term\Enums\ReviewSessionStatus;
use App\Core\Modules\Term\Enums\ReviewStatisticsPeriod;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

final class GetUserReviewPeriodStatisticsActionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow('2026-11-08 12:00:00 UTC');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_it_aggregates_finished_sessions_accuracy_mastery_and_local_activity_streak(): void
    {
        $userId = DB::table('users')->insertGetId(['name' => 'Learner']);
        $termId = DB::table('terms')->insertGetId(['text' => 'study', 'type' => 'word']);
        $variantId = DB::table('term_variants')->insertGetId([
            'term_id' => $termId,
            'pos' => 'noun',
            'created_at' => now(),
        ]);
        DB::table('learning_progress')->insert([
            'user_id' => $userId,
            'variant_id' => $variantId,
            'repetitions' => 5,
            'interval' => 1,
            'ease_factor' => 2.5,
            'due_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $nextVariantId = DB::table('term_variants')->insertGetId([
            'term_id' => $termId,
            'pos' => 'verb',
            'created_at' => now(),
        ]);
        DB::table('learning_progress')->insert([
            'user_id' => $userId,
            'variant_id' => $nextVariantId,
            'repetitions' => 0,
            'interval' => 0,
            'ease_factor' => 2.5,
            'due_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $sessionId = $this->createSession(
            $userId,
            ReviewSessionStatus::FINISHED,
            '2026-11-01 18:01:00 UTC',
            '2026-11-01 17:40:00 UTC',
        );
        $this->createAnsweredItem($sessionId, $variantId, true, '2026-11-01 17:50:00 UTC');
        $this->createAnsweredItem($sessionId, $nextVariantId, true, '2026-11-01 18:00:00 UTC');

        $secondSessionId = $this->createSession(
            $userId,
            ReviewSessionStatus::FINISHED,
            '2026-11-03 00:01:00 UTC',
            '2026-11-02 23:40:00 UTC',
        );
        $this->createAnsweredItem($secondSessionId, $nextVariantId, false, '2026-11-03 00:00:00 UTC');

        $thirdSessionId = $this->createSession(
            $userId,
            ReviewSessionStatus::FINISHED,
            '2026-11-05 10:01:00 UTC',
            '2026-11-05 09:40:00 UTC',
        );
        $this->createAnsweredItem($thirdSessionId, $variantId, true, '2026-11-05 10:00:00 UTC');

        $activeSessionId = $this->createSession($userId, ReviewSessionStatus::ACTIVE, null);
        $this->createAnsweredItem($activeSessionId, $nextVariantId, true, '2026-11-06 10:00:00 UTC');

        $statistics = app(GetUserReviewPeriodStatisticsAction::class)->run(
            $userId,
            ReviewStatisticsPeriod::WEEK,
            6,
        );

        self::assertSame(3, $statistics->sessionsCount);
        self::assertSame(4, $statistics->answeredTermsCount);
        self::assertSame(75.0, $statistics->correctAnswersPercent);
        self::assertSame(1, $statistics->wellLearnedWordsCount);
        self::assertSame(3, $statistics->activeDaysCount);
        self::assertSame(2, $statistics->longestActivityStreak);
    }

    public function test_empty_period_returns_zero_statistics(): void
    {
        $userId = DB::table('users')->insertGetId(['name' => 'Learner']);

        $statistics = app(GetUserReviewPeriodStatisticsAction::class)->run(
            $userId,
            ReviewStatisticsPeriod::DAY,
            0,
        );

        self::assertSame(0, $statistics->sessionsCount);
        self::assertSame(0, $statistics->answeredTermsCount);
        self::assertSame(0.0, $statistics->correctAnswersPercent);
        self::assertSame(0, $statistics->wellLearnedWordsCount);
        self::assertSame(0, $statistics->activeDaysCount);
        self::assertSame(0, $statistics->longestActivityStreak);
    }

    private function createSession(
        int $userId,
        ReviewSessionStatus $status,
        ?string $finishedAt,
        ?string $startedAt = null,
    ): int {
        return DB::table('review_sessions')->insertGetId([
            'user_id' => $userId,
            'status' => $status->value,
            'started_at' => $startedAt ?? now()->subMinute(),
            'finished_at' => $finishedAt,
        ]);
    }

    private function createAnsweredItem(int $sessionId, int $variantId, bool $isCorrect, string $answeredAt): void
    {
        DB::table('review_session_items')->insert([
            'session_id' => $sessionId,
            'variant_id' => $variantId,
            'is_correct' => $isCorrect,
            'presented_at' => Carbon::parse($answeredAt)->subSeconds(10),
            'answered_at' => $answeredAt,
        ]);
    }
}
