<?php

namespace App\Actions\Endusers\Members;

use App\Models\Endusers\Member;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class ReadMembers
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
            'per_page' => 'sometimes|nullable',
            'client_id' => 'exists:clients,id|required',
        ];
    }

    public function handle($data, $user = null)
    {
        if (array_key_exists('per_page', $data)) {
            $page_count = $data['per_page'] > 0 && $data['per_page'] < 1000 ? $data['per_page'] : 10;
        } else {
            $page_count = 10;
        }

        $members_model = Member::whereClientId($data['client_id'])
            ->with('location')
            ->with('notes')
            ->orderBy('created_at', 'desc')
            ->sort()
            ->paginate($page_count)
            ->appends(request()->except('page'));


        return $members_model;
    }

    public function asController(ActionRequest $request)
    {
        $members = $this->handle(
            $request->validated(),
            $request->user()
        );

        if ($request->wantsJson()) {
            return $members;
        }
    }
}
