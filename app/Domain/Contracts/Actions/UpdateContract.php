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

class UpdateContract
{
    use AsAction;

    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string','max:50'],
        ];
    }

    public function handle(Contract $contract, array $data): Contract
    {
        ContractAggregate::retrieve($contract->id)
            ->update($data)
            ->persist();

        return $contract->refresh();
    }

    /**
     * @return string[]
     */
    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('contract.update', Contract::class);
    }

    public function asController(ActionRequest $request, Contract $contract): Contract
    {
        $data = $request->validated();

        return $this->handle(
            $contract,
            $data
        );
    }

    public function htmlResponse(Contract $contract): RedirectResponse
    {
        Alert::success("Contract '{$contract->name}' was updated")->flash();

        return Redirect::back();
    }
}
