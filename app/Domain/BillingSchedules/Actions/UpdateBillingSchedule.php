<?php

declare(strict_types=1);

namespace App\Domain\BillingSchedules\Actions;

use App\Domain\BillingSchedules\BillingScheduleAggregate;
use App\Domain\BillingSchedules\Projections\BillingSchedule;
use App\Http\Middleware\InjectClientId;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateBillingSchedule
{
    use AsAction;

    public function handle(BillingSchedule $billing_schedule, array $payload): BillingSchedule
    {
        BillingScheduleAggregate::retrieve($billing_schedule->id)->update($payload)->persist();

        return $billing_schedule->refresh();
    }

    public function rules(): array
    {
        return [
            'client_id' => 'required',
            'type' => 'required',
            'is_open_ended' => ['sometimes', 'boolean'],
            'is_renewable' => ['sometimes', 'boolean'],
            'should_renew_automatically' => ['sometimes', 'boolean'],
            'term_length' => 'sometimes',
            'min_terms' => 'sometimes',
            'amount' => ['sometimes', 'float' ],

        ];
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function asController(ActionRequest $request, BillingSchedule $billing_schedule): BillingSchedule
    {
        $data = $request->validated();

        return $this->handle(
            $billing_schedule,
            $data
        );
    }
}
