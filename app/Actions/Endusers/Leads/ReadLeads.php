<?php

namespace App\Actions\Endusers\Leads;

use App\Models\Endusers\Lead;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class ReadLeads
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

    public function handle($data)
    {
        if (array_key_exists('per_page', $data)) {
            $page_count = $data['per_page'] > 0 && $data['per_page'] < 1000 ? $data['per_page'] : 10;
        } else {
            $page_count = 10;
        }

        $prospects_model = Lead::whereClientId($data['client_id'])
            ->with('location')
            ->with('leadType')
            ->with('membershipType')
            ->with('leadSource')
            ->with('leadsclaimed')
            ->with('notes')
            ->orderBy('created_at', 'desc')
            ->sort()
            ->paginate($page_count)
            ->appends(request()->except('page'));

        return $prospects_model;
    }

    public function asController(ActionRequest $request)
    {
        $leads = $this->handle(
            $request->validated(),
        );

        if ($request->wantsJson()) {
            return $leads;
        }
    }
}
