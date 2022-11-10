<?php

namespace App\Domain\Agreements\Actions;

use App\Domain\Agreements\AgreementAggregate;
use App\Domain\Agreements\Projections\Agreement;
use App\Http\Middleware\InjectClientId;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class UpdateAgreement
{
    use AsAction;

    public function handle(Agreement $audience, array $payload): Agreement
    {
        AgreementAggregate::retrieve($audience->id)->update($payload)->persist();

        return $audience->refresh();
    }

    public function rules(): array
    {
        return [
            'agreement_category_id' => ['required', 'exists:agreement_categories,id'],
            'gr_location_id' => ['sometimes', 'string'],
            'agreement_json' => ['sometimes', 'json'],
        ];
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function asController(ActionRequest $request, Agreement $agreement): Agreement
    {
        $data = $request->validated();

        return $this->handle(
            $agreement,
            $data
        );
    }

    public function htmlResponse(Agreement $agreement): RedirectResponse
    {
        Alert::success("Agreement '{$agreement->name}' was created")->flash();

        return Redirect::route('agreements.edit', $agreement->id);
    }
}
