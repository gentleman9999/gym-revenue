<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Conversations\Twilio\Actions\StartConversation;
use App\Domain\Conversations\Twilio\Exceptions\ConversationException;
use App\Domain\EndUsers\Leads\Projections\Lead;
use App\Domain\EndUsers\Members\Projections\Member;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use InvalidArgumentException;

class ChatController extends Controller
{
    public function index(Request $request, string $end_user_type = null, string $id = null): Response
    {
        $error = null;
        if ($end_user_type !== null && $id !== null) {
            try {
                StartConversation::run($request->user(), match ($end_user_type) {
                    'lead' => Lead::find($id),
                    'member' => Member::find($id),
                    default => throw new InvalidArgumentException("'{$end_user_type}' is not a valid end user type.")
                });
            } catch (ConversationException $throwable) {
                $error = $throwable->getMessage();
            }
        }

        return Inertia::render('Chat/Index', [
            'error' => $error,
        ]);
    }
}
