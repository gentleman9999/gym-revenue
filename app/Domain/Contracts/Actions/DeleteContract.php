<?php

declare(strict_types=1);

namespace App\Domain\Contracts\Actions;

use App\Domain\Contracts\ContractAggregate;
use App\Domain\Contracts\Projections\Contract;
use App\Http\Middleware\InjectClientId;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class DeleteContract
{
    use AsAction;

    public function handle(string $contract_id): bool
    {
        ContractAggregate::retrieve($contract_id)->delete()->persist();

        return true;
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('contract.delete', Contract::class);
    }

    public function asController(string $contract_id): bool
    {
        return $this->handle($contract_id);
    }

    public function htmlResponse(): RedirectResponse
    {
        Alert::success("Contract was deleted")->flash();

        return Redirect::back();
    }
}
