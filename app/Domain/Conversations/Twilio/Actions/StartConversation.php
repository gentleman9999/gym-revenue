<?php

declare(strict_types=1);

namespace App\Domain\Conversations\Twilio\Actions;

use App\Domain\Conversations\Twilio\ClientConversationAggregates;
use App\Domain\EndUsers\Projections\EndUser;
use App\Domain\Users\Models\User;
use Lorisleiva\Actions\Concerns\AsAction;
use Ramsey\Uuid\Uuid;

class StartConversation
{
    use AsAction;

    public function handle(User $user, EndUser $end_user)
    {
        ClientConversationAggregates::retrieve((string) Uuid::uuid4())->clientConversationCreated([
            'user_id' => $user->id,
            'end_user' => [$end_user::class, $end_user->id],
        ])->persist();
    }
}
