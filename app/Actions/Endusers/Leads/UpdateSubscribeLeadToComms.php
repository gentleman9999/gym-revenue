<?php

namespace App\Actions\Endusers\Leads;

use App\Aggregates\Endusers\LeadAggregate;
use App\Models\Endusers\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateSubscribeLeadToComms
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
        LeadAggregate::retrieve($id)->subscribeToComms($data['email'], $data['sms'], Carbon::now())->persist();

        return Lead::findOrFail($id);
    }

    public function asController(Request $request, $id)
    {
        $this->handle(
            $id,
            $request->validated(),
        );
    }
}
