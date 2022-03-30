<?php

namespace App\Actions\Clients\Classifications;

use App\Aggregates\Clients\ClientAggregate;
use App\Helpers\Uuid;
use App\Models\Clients\Classification;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Prologue\Alerts\Facades\Alert;
use Lorisleiva\Actions\Concerns\AsAction;


class CreateClassification
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
        ];
    }

    public function handle($data, $current_user = null)
    {

        if(!is_null($current_user)) {
            $client_id = $current_user->currentClientId();
            $data['client_id'] = $client_id;
        }

        $id = Uuid::new();
        $data['id'] = $id;

        ClientAggregate::retrieve($client_id ?? $data['client_id'])->createClassification($current_user->id ?? "Auto Generated", $data)->persist();

        return Classification::findOrFail($id);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();
        return $current_user->can('classifications.create', Classification::class);
    }

    public function asController(ActionRequest $request)
    {
        $classification = $this->handle(
            $request->validated(),
            $request->user(),
        );

        Alert::success("Classification '{$classification->title}' was created")->flash();

        return Redirect::route('classifications');
    }

}
