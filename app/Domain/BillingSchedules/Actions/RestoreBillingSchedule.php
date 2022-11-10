<?php

namespace App\Domain\BillingSchedules\Actions;

use App\Domain\BillingSchedules\BillingScheduleAggregate;
use App\Domain\BillingSchedules\Projections\BillingSchedule;
use Illuminate\Console\Command;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class RestoreBillingSchedule
{
    use AsAction;

    public string $commandSignature = 'audience:restore {id}';
    public string $commandDescription = 'Restores the audience';

    public function handle(BillingSchedule $billing_schedule): BillingSchedule
    {
        BillingScheduleAggregate::retrieve($billing_schedule->id)->restore()->persist();

        return $billing_schedule->refresh();
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('*');
    }

    public function asCommand(Command $command): void
    {
        $billing_schedule = $this->handle($command->argument('id'));
        $command->info('Restored Billing Schedule ' . $billing_schedule->type);
    }
}
