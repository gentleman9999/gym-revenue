<?php

namespace App\Domain\Positions\Actions;

use App\Domain\Positions\PositionAggregate;
use App\Models\Position;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class TrashPosition
{
    use AsAction;

    public function handle(Position $position): Position
    {
        PositionAggregate::retrieve($position->id)->trash()->persist();

        return $position->refresh();
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('positions.trash', Position::class);
    }

    public function asController(ActionRequest $request, Position $position): Position
    {
        return $this->handle(
            $position
        );
    }

    public function htmlResponse(Position $position): RedirectResponse
    {
        Alert::success("Position '{$position->name}' was trashed")->flash();

        return Redirect::back();
    }
}
