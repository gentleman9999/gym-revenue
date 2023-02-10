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

    public function handle(?string $queue_name): void
    {
        $message = 'Queue Job Ran';
        if ($queue_name) {
            $message .= " on queue $queue_name";
            self::dispatch()->delay(now()->addSeconds(30))->onQueue($queue_name);
        } else {
            self::dispatch()->delay(now()->addSeconds(30));
        }
        
        Log::info($message);
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
        $this->handle($request->queue_name);
    }

    public function htmlResponse(): RedirectResponse
    {
        Alert::success('Test job successfully queued')->flash();

        return Redirect::route('dashboard');
    }
}