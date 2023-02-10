<?php

declare(strict_types=1);

namespace App\Domain\Positions\Actions;

use App\Domain\Positions\PositionAggregate;
use App\Models\Position;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class DeletePosition
{
    use AsAction;

    public function handle(Position $position): Position
    {
        PositionAggregate::retrieve($position->id)->delete()->persist();

        return $position;
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('positions.create', Position::class);
    }

    public function asController(ActionRequest $request, Position $position): Position
    {
        return $this->handle(
            $position,
        );
    }

    public function htmlResponse(Position $position): RedirectResponse
    {
        Alert::success("Position '{$position->name}' was deleted")->flash();

        return Redirect::route('positions.edit', $position->id);
    }
}
