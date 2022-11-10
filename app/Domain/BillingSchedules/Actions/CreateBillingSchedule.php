<?php

declare(strict_types=1);

namespace App\Domain\BillingSchedules\Actions;

use App\Domain\BillingSchedules\BillingScheduleAggregate;
use App\Domain\BillingSchedules\Projections\BillingSchedule;
use App\Support\Uuid;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateBillingSchedule
{
    use AsAction;

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
            'amount' => 'sometimes',

        ];
    }

    public function handle(array $data): BillingSchedule
    {
        $id = Uuid::new();
        BillingScheduleAggregate::retrieve($id)->create($data)->persist();


        return BillingSchedule::findOrFail($id);
    }
}
