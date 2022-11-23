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

class RestoreContract
{
    use AsAction;

    public function handle(Contract $contract): Contract
    {
        ContractAggregate::retrieve($contract->id)->restore()->persist();

        return $contract->refresh();
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('contract.restore', Contract::class);
    }

    public function asController(Contract $contract): Contract
    {
        return $this->handle($contract);
    }

    public function htmlResponse(Contract $contract): RedirectResponse
    {
        Alert::success("Contract '{$contract->name}' restored.")->flash();

        return Redirect::back();
    }
}
