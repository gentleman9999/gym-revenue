<?php

declare(strict_types=1);

namespace App\Domain\Positions\Actions;

use App\Actions\GymRevAction;
use App\Domain\Positions\PositionAggregate;
use App\Models\Position;
use App\Support\Uuid;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Prologue\Alerts\Facades\Alert;

class CreatePosition extends GymRevAction
{
    /**
     * Get the validation rules that apply to the action.
     *
     * @return array<string, array<string>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string','min:2'],
            'client_id' => ['string', 'required'],
            'departments' => ['array', 'sometimes'],
        ];
    }

    /**
     * @param array<string, mixed> $data
     *
     */
    public function handle(array $data): Position
    {
        $id = Uuid::get();

        $aggy = PositionAggregate::retrieve($id);

        $aggy->create($data)->persist();

        return Position::findOrFail($id);
    }

    // public function __invoke($_, array $args): Position
    // {
    //     return $this->handle($args);
    // }

    /**
     * @param array<string, mixed> $args
     *
     * @return array
     */
    public function mapArgsToHandle(array $args): array
    {
        return [$args['input']];
    }

    // public function getControllerMiddleware(): array
    // {
    //     return [InjectClientId::class];
    // }

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
