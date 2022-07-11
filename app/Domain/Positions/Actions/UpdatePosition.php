<?php

namespace App\Domain\Positions\Actions;

use App\Domain\Positions\PositionAggregate;
use App\Models\Position;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class UpdatePosition
{
    use AsAction;

    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['string', 'required'],
        ];
    }

    public function handle(string $id, array $payload): Position
    {
        PositionAggregate::retrieve($id)->update($payload)->persist();

        return Position::findOrFail($id);
    }

    public function asController(ActionRequest $request, Position $position)
    {
        $position = $this->handle(
            $position->id,
            $request->validated(),
        );

        Alert::success("Position '{$position->name}' was updated")->flash();

        return Redirect::route('positions.edit', $position->id);
    }
}
