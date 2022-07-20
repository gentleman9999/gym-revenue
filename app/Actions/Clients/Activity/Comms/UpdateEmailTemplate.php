<?php

namespace App\Actions\Clients\Activity\Comms;

use App\Aggregates\Clients\ClientAggregate;
use App\Models\Comms\EmailTemplates;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class UpdateEmailTemplate
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
//            'client_id' => ['required', 'exists:clients,id'],
            'name' => ['string', 'sometimes', 'required'],
            'subject' => ['string', 'sometimes', 'required'],
            'markup' => ['string', 'sometimes', 'required'],
            'json' => ['required'],
//            'thumbnail' => ['string', 'required']
        ];
    }

    public function handle($data, $user = null)
    {
        //TODO: client_id no longer provided because dirty(), and it should only need an id once we merge in with
        //my new branch, where everything is not tied to the client aggregate.
        $client_id = $data['id'] ?? $user->client_id;
        ClientAggregate::retrieve($client_id)
            ->updateEmailTemplate($user->id ?? "Auto Generated", $data)
            ->persist();

        return EmailTemplates::findOrFail($data['id']);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('email-templates.update', EmailTemplates::class);
    }

    public function asController(ActionRequest $request, $id)
    {
        $data = $request->validated();
        if (! count($data)) {
            return Redirect::back();
        }
        $data['id'] = $id;
        $template = $this->handle(
            $data,
            $request->user()
        );

        Alert::success("Email Template'{$template->name}' was updated")->flash();

        return Redirect::route('comms.email-templates.edit', $template->id);
        //redirecting back causes infinite loop in inertia modals.
//        return Redirect::back();
    }
}
