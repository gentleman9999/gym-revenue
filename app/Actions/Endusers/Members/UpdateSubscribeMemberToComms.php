<?php

namespace App\Actions\Endusers\Members;

use App\Aggregates\Endusers\MemberAggregate;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateSubscribeMemberToComms
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
        MemberAggregate::retrieve($id)->subscribeToComms($data['email'], $data['sms'], Carbon::now())->persist();
    }

    public function asController(Request $request, $id)
    {
        $this->handle(
            $id,
            $request->validated(),
        );
    }
}
