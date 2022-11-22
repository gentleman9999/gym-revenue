<?php

declare(strict_types=1);

namespace App\Domain\Contracts\Actions;

use App\Domain\Contracts\ContractAggregate;
use App\Domain\Contracts\Projections\Contract;
use App\Http\Middleware\InjectClientId;
use App\Support\Uuid;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class CreateContract
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
            'name' => ['required', 'string','max:50'],
            'client_id' => ['string', 'required'],
        ];
    }

    public function handle(array $data): Contract
    {
        $id = Uuid::get();
        ContractAggregate::retrieve($id)->create($data)->persist();

        return Contract::findOrFail($id);
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('contract.create', Contract::class);
    }

    public function asController(ActionRequest $request): \Illuminate\Http\RedirectResponse
    {
        $data = $request->validated();
        $contract = $this->handle($data);

        Alert::success("Contract '{$contract->name}' was created")->flash();

        return Redirect::back();
    }
}
