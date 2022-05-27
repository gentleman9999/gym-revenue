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
            'client_id' => ['required', 'exists:clients,id'],
            'name' => ['string', 'required'],
            'subject' => ['string', 'required'],
            'markup' => ['string', 'required'],
            'json' => ['required'],
//            'thumbnail' => ['string', 'required']
        ];
    }

    public function handle($data, $user = null)
    {
        ClientAggregate::retrieve($data['client_id'])
            ->updateEmailTemplate($user->id ?? "Auto Generated", $data)
            ->persist();

        return EmailTemplates::findOrFail($data['id']);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('email-templates.create', EmailTemplates::class);
    }

    public function asController(ActionRequest $request, $id)
    {
        $data = $request->validated();
        $data['id'] = $id;
        $template = $this->handle(
            $data,
            $request->user()
        );

        Alert::success("Email Template'{$template->name}' was updated")->flash();

//        return Redirect::route('comms.email-templates.edit', $template->id);
        return Redirect::back();
    }
}
