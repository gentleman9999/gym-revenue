<?php

namespace App\Domain\Positions\Actions;

use App\Domain\Positions\PositionAggregate;
use App\Http\Middleware\InjectClientId;
use App\Models\Position;
use App\Support\Uuid;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class CreatePosition
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
            'name' => ['required', 'string','min:2'],
            'client_id' => ['string', 'required'],
            'departments' => ['array', 'sometimes'],
        ];
    }

    public function handle($data): Position
    {
        $id = Uuid::new();

        $aggy = PositionAggregate::retrieve($id);

        $aggy->create($data)->persist();

        return Position::findOrFail($id);
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('positions.create', Position::class);
    }

    public function asController(ActionRequest $request): Position
    {
        return $this->handle(
            $request->validated(),
        );
    }

    public function htmlResponse(Position $position): RedirectResponse
    {
        Alert::success("Position '{$position->name}' was created")->flash();

        return Redirect::route('positions.edit', $position->id);
    }
}
