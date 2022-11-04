<?php

namespace App\Domain\Agreements\Actions;

use App\Domain\Agreements\AgreementAggregate;
use App\Domain\Agreements\Projections\Agreement;
use App\Http\Middleware\InjectClientId;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

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
}
