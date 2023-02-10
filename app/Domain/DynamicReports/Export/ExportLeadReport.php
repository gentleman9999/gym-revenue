<?php

declare(strict_types=1);

namespace App\Domain\DynamicReports\Export;

use App\Domain\Users\Models\Lead;
use App\Exports\ReportsExport;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Maatwebsite\Excel\Facades\Excel;
use Prologue\Alerts\Facades\Alert;
use Spatie\QueryBuilder\QueryBuilder;

class ExportLeadReport
{
    use AsAction;

    public function handle($data): QueryBuilder
    {
        return QueryBuilder::for(Lead::class)
            ->allowedFilters(['first_name', 'last_name', 'email']);
    }

    public function asController(ActionRequest $request)
    {
        return $this->handle($request->all());
    }

    public function htmlResponse($data): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        Alert::success("Reported Generated")->flash();

        return Excel::download(new ReportsExport($data), 'report.csv');
    }
}
