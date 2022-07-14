<?php

namespace App\Actions\Endusers\Members;

use App\Domain\Leads\Actions\MarkLeadConverted;
use App\Domain\Leads\Models\Lead;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class CheckIfMemberWasLead
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
            'member_id' => 'required',
            'email' => ['required', 'email:rfc,dns'],
            'external_id' => 'nullable|array',
        ];
    }

    public function handle($data)
    {
        if (array_key_exists('external_id', $data)) {
            $lead = Lead::whereEmail($data['email'])
                ->orWhere('external_id', $data['external_id'])
                ->first();
        } else {
            $lead = Lead::whereEmail($data['email'])
                ->first();
        }

        if (! is_null($lead)) {
            MarkLeadConverted::run([
                'member_id' => $data['member_id'],
                'id' => $lead->id,
                ]);
        }

        return true;
    }

    public function asController(ActionRequest $request, $id)
    {
        $data = $request->validated();
        $data['id'] = $id;
        $member = $this->handle(
            $data
        );
    }
}
