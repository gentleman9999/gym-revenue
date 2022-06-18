<?php

namespace App\Actions\Endusers\Members;

use App\Aggregates\Endusers\MemberAggregate;
use App\Models\Endusers\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateMemberCommunicationPreferences
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
            'email' => ['required', 'boolean'],
            'sms' => ['required', 'boolean'],
        ];
    }

    public function handle($id, $data)
    {
        MemberAggregate::retrieve($id)->updateCommunicationPreferences($data['email'], $data['sms'], Carbon::now())->persist();

        return Member::findOrFail($id);
    }

    public function asController(Request $request, $id)
    {
        $this->handle(
            $id,
            $request->validated(),
        );
    }
}
