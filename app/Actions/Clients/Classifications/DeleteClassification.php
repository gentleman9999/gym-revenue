<?php

namespace App\Actions\Clients\Classifications;

use App\Models\Clients\Classification;
use App\Aggregates\Clients\ClientAggregate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;


class DeleteClassification
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
            //no rules since we only accept an id route param, which is validated in the route definition
        ];
    }

    public function handle($current_user, $id)
    {
        $client_id = $current_user->currentClientId();
        ClientAggregate::retrieve($client_id)->deleteClassification($current_user->id, $id)->persist();
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();
        return $current_user->can('classifications.delete', Classification::class);
    }

    public function asController(Request $request, $id)
    {
        $Classification = Classification::findOrFail($id);
        $this->handle(
            $request->user(),
            $id
        );

        Alert::success("Classification '{$Classification->title}' was deleted")->flash();

        return Redirect::route('classifications');

    }
}
