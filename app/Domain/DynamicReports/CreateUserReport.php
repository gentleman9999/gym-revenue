<?php

namespace App\Domain\DynamicReports;

use App\Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Inertia\Inertia;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;
use Spatie\QueryBuilder\QueryBuilder;

class CreateUserReport
{
    use AsAction;

    public function handle($data): Collection
    {
        $report = QueryBuilder::for(User::class)
            ->allowedFilters(['first_name', 'last_name', 'email', 'alternate_email'])
                   ->get();
        //dd($report->toSql());

        return $report;
    }

    public function asController(ActionRequest $request)
    {
        return $this->handle($request->all());
    }

    public function htmlResponse($data): \Inertia\Response
    {
        Alert::success("Reported Generated")->flash();

        return Inertia::render('DynamicReports/ReportsDashboard', [
            'report' => $data,
        ]);
    }
}
