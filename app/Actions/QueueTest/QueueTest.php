<?php

declare(strict_types=1);

namespace App\Actions\QueueTest;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class QueueTest
{
    use AsAction;

    public function handle(): void
    {
        Log::info('Queue Job Ran');

        self::dispatch()->delay(now()->addSeconds(30));
    }

    public function authorize(ActionRequest $request): bool
    {
        $user = $request->user();

        if ($user) {
            if (in_array($user->email, ['mmonim@gymrevenue.com', 'philip@capeandbay.com', 'blair@capeandbay.com'])) {
                return true;
            }
        }

        return false;
    }

    public function asController(ActionRequest $request): void
    {
        $this->handle();
    }

    public function htmlResponse(): RedirectResponse
    {
        Alert::success('Test job successfully queued'); 

        return Redirect::back();
    }
}