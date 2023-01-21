<?php

declare(strict_types=1);

namespace App\Domain\Users\Actions;

use App\Domain\Notes\Model\Note;
use App\Domain\Users\Aggregates\UserAggregate;
use App\Domain\Users\Models\EndUser;
use Inertia\Inertia;
use Inertia\Response;
use Lorisleiva\Actions\Concerns\AsAction;

class ShowCustomer
{
    use AsAction;

    public function handle(EndUser $end_user): array
    {
        $aggy = UserAggregate::retrieve($end_user->id);
        $preview_note = Note::select('note')->whereEntityId($end_user->id)->get();

        return [
            'customer' => $end_user,
            'preview_note' => $preview_note,
            'interactionCount' => $aggy->getInteractionCount(),
            'hasTwilioConversation' => $end_user->client->hasTwilioConversationEnabled(),
        ];
    }

    public function htmlResponse(array $data): Response
    {
        return Inertia::render('Customers/Show', [
            'customer' => $data['customer'],
            'preview_note' => $data['preview_note'],
            'interactionCount' => $data['interactionCount'],
            'hasTwilioConversation' => $data['hasTwilioConversation'],
        ]);
    }
}
