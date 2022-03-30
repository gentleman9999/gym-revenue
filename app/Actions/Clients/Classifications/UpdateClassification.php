<?php

namespace App\Actions\Clients\Classifications;

use App\Aggregates\Clients\ClientAggregate;
use App\Models\Clients\Classification;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Prologue\Alerts\Facades\Alert;
use Lorisleiva\Actions\Concerns\AsAction;


class UpdateClassification
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
            'title' => ['string', 'required'],
            'id' => ['string', 'required'],
        ];
    }

    public function handle($data, $current_user)
    {
        $client_id = $current_user->currentClientId();
        $data['client_id'] = $client_id;
        ClientAggregate::retrieve($client_id)->updateClassification($current_user->id, $data)->persist();

        return Classification::find($data['id']);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();
        return $current_user->can('classifications.update', Classification::class);
    }

    public function asController(ActionRequest $request, $id)
    {
        $data = $request->validated();
        $data['id'] = $id;
        $Classification = $this->handle(
            $data,
            $request->user(),
        );

        Alert::success("Classification '{$Classification->title}' was updated")->flash();

        return Redirect::route('classifications');
    }

}
