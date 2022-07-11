<?php

namespace App\Domain\Positions\Actions;

use App\Domain\Positions\PositionAggregate;
use App\Domain\Reminders\Reminder;
use App\Support\Uuid;
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
    public function rules()
    {
        return [
            'name' => ['string', 'required'],
        ];
    }

    public function handle($data, $current_user = null)
    {
        if (! is_null($current_user)) {
            $client_id = $current_user->currentClientId();
            $data['client_id'] = $client_id;
        }

        $id = Uuid::new();

        $aggy = PositionAggregate::retrieve($id);

        $aggy->create($data)->persist();

        return Reminder::findOrFail($id);
    }

    public function authorize(ActionRequest $request): bool
    {
        return true;
    }

    public function asController(ActionRequest $request)
    {
        $position = $this->handle(
            $request->validated(),
            $request->user(),
        );

        Alert::success("Position '{$position->name}' was created")->flash();

        return Redirect::route('positions.update', $position->id);
    }
}
