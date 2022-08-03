<?php

namespace App\Domain\SMS\Actions;

use App\Models\SmsTracking;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class StatusCallback
{
    use AsAction;

    public function handle(array $payload)
    {
        $test = $payload;

        SmsTracking::create($payload);

        return $test;
    }

    public function asController(ActionRequest $request)
    {
        return $this->handle(
            $request->all()
        );
    }

    public function jsonResponse($result)
    {
        $results = ['success' => false, 'message' => 'Server Error. Message not sent'];
        $code = 500;

        if ($result) {
            if (is_string($result)) {
                $results['message'] = $result;
                $code = 401;
            } else {
                $results['success'] = true;
                $results['message'] = 'Success';
                $code = 200;
            }
        }

        return response($results, $code);
    }
}
