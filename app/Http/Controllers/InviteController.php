<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\CalendarAttendees\CalendarAttendee;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class InviteController extends Controller
{
    public function index(string $id): InertiaResponse
    {
        //decrypt ID when we decide to obfuscate it in the future
        return Inertia::render('Invite/Show', [
            'attendeeData' => CalendarAttendee::whereId($id)->with('event')->first(),
        ]);
    }
}
