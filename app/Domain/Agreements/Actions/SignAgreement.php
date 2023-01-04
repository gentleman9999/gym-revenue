<?php

declare(strict_types=1);

namespace App\Domain\Agreements\Actions;

use App\Domain\Agreements\AgreementAggregate;
use App\Domain\Agreements\Projections\Agreement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class SignAgreement
{
    use AsAction;

    public function rules(): array
    {
        return [
            'id' => ['required','string'],
        ];
    }

    public function handle(array $agreement): Agreement
    {
        AgreementAggregate::retrieve($agreement['id'])->sign($agreement)->persist();

        return Agreement::findOrFail($agreement['id']);
    }

    public function asController(ActionRequest $request)
    {
        $this->handle($request->validated());
    }

    public function htmlResponse(): RedirectResponse
    {
        Alert::success("Agreement is signed successfully")->flash();

        return Redirect::back();
    }
}
