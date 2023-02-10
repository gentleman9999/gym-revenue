<?php

declare(strict_types=1);

namespace App\Domain\Positions\Actions;

use App\Actions\GymRevAction;
use App\Domain\Positions\PositionAggregate;
use App\Models\Position;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Prologue\Alerts\Facades\Alert;

class UpdatePosition extends GymRevAction
{
    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => ['string', 'required'],
            'departments' => ['array', 'sometimes'],
        ];
    }

    /**
     * @param array<string, mixed> $args
     *
     * @return array<Position|null, string>
     */
    public function mapArgsToHandle(array $args): array
    {
        $position = $args['input'];

        return [Position::find($position['id']), $position];
    }

    public function handle(Position $position, array $data): Position
    {
        PositionAggregate::retrieve($position->id)->update($data)->persist();

        return $position->refresh();
    }

    // public function __invoke($_, array $args): Position
    // {
    //     $position = Position::find($args['id']);

    //     return $this->handle($position, $args);
    // }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('positions.update', Position::class);
    }

    public function asController(ActionRequest $request, Position $position): Position
    {
        return $this->handle(
            $position,
            $request->validated()
        );
    }

    public function htmlResponse(Position $position): RedirectResponse
    {
        Alert::success("Position '{$position->name}' was updated")->flash();

        return Redirect::route('positions.edit', $position->id);
    }
}
