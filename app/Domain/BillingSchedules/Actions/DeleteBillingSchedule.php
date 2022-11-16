<?php

namespace App\Domain\BillingSchedules\Actions;

use App\Domain\BillingSchedules\BillingScheduleAggregate;
use App\Domain\BillingSchedules\Projections\BillingSchedule;
use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteBillingSchedule
{
    use AsAction;

    public function handle(string $id): BillingSchedule
    {
        $billing_schedule = BillingSchedule::withTrashed()->findOrFail($id);
        BillingScheduleAggregate::retrieve($id)->delete()->persist();

        return $billing_schedule;
    }

    public function asCommand(Command $command): void
    {
        $billing_schedule = BillingSchedule::withTrashed()->findOrFail($command->argument('id'));
        if ($command->confirm("Are you sure you want to hard delete Billing Schedule '{$billing_schedule->type}'? This cannot be undone")) {
            $billing_schedule = $this->handle($command->argument('id'));
            $command->info('Deleted Billing Schedule ' . $billing_schedule->type);

            return;
        }
        $command->info('Aborted deleting Billing Schedule ' . $billing_schedule->type);
    }
}
