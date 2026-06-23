<?php
declare(strict_types=1);

namespace App\Core\Modules\Term\Actions;

use App\Core\Common\Parents\Action;
use App\Core\Modules\Term\Jobs\ProcessUserTermCandidatesJob;
use App\Core\Modules\Term\Jobs\ResolveTermCandidatesJob;
use App\Core\Modules\Term\Jobs\TranslateTermsJob;
use App\Core\Modules\Term\Normalizers\TermNormalizer;
use App\Core\Modules\Term\SubActions\StoreTermCandidatesForUserSubAction;
use App\Core\Modules\Term\Validators\TermValidator;
use Illuminate\Support\Facades\Bus;
use Throwable;

final class AddUserTermsAction extends Action
{
    public function __construct(
        private readonly TermNormalizer $normalizer,
        private readonly TermValidator $validator,
        private readonly StoreTermCandidatesForUserSubAction $storeCandidatesForUserSubAction,
    ){}

    /**
     * @param string[] $terms
     * @throws Throwable
     */
    public function run(int $userId, array $terms): void
    {
        $terms = $this->validator->validate(
            $this->normalizer->normalize($terms),
        );

        if (empty($terms)) {
            return;
        }

        $this->storeCandidatesForUserSubAction->run($userId, $terms);

        Bus::chain([
            new ResolveTermCandidatesJob(),
            new TranslateTermsJob(),
            new ProcessUserTermCandidatesJob(),
        ])->dispatch();
    }
}