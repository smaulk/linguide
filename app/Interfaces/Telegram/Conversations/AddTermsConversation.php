<?php
declare(strict_types=1);

namespace App\Interfaces\Telegram\Conversations;

use App\Core\Modules\Term\Actions\AddUserTermsAction;
use App\Interfaces\Telegram\Classes\AppUserContext;
use App\Interfaces\Telegram\Parents\Conversation;
use Psr\SimpleCache\InvalidArgumentException;
use SergiX44\Nutgram\Nutgram;
use Throwable;

final class AddTermsConversation extends Conversation
{
    public function __construct(
        private readonly AppUserContext $userContext,
        private readonly AddUserTermsAction $addTermsAction,
    ){}

    /**
     * @throws InvalidArgumentException
     */
    public function start(Nutgram $bot): void
    {
        $bot->sendMessage('Отправьте список терминов (каждый термин с новой строки)');
        $this->next('addTerms');
    }

    /**
     * @throws Throwable
     * @throws InvalidArgumentException
     */
    public function addTerms(Nutgram $bot): void
    {
        $messageText = $bot->message()?->getText();
        if ($messageText === null) {
            return;
        }

        $appUser = $this->userContext->get($bot);
        $terms = $this->parseTerms($messageText);

        $this->addTermsToUser($appUser->id, $terms);

        $bot->sendMessage("Термины переданы в обработку!✅",);
        $this->end();
    }

    /**
     * @param string[] $terms
     * @throws Throwable
     */
    private function addTermsToUser(int $userId, array $terms): void
    {
        if (empty($terms)) {
            return;
        }

       $this->addTermsAction->run($userId, $terms);
    }

    /**
     * @param string $input
     * @return string[]
     */
    private function parseTerms(string $input): array
    {
        return preg_split('/[\n,;]+/', $input) ?: [];
    }
}