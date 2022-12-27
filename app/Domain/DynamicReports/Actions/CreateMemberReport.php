<?php

namespace App\Domain\DynamicReports\Actions;

use App\Domain\Users\Models\Member;
use Illuminate\Database\Eloquent\Collection;
use Inertia\Inertia;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;
use Spatie\QueryBuilder\QueryBuilder;

class CreateMemberReport
{
    use AsAction;

    public function handle($data): Collection
    {
        $report = QueryBuilder::for(Member::class)
            ->allowedFilters(['first_name', 'last_name', 'email'])
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
