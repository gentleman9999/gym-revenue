<?php

namespace App\Domain\Leads\Actions;

use App\Domain\Leads\LeadAggregate;
use App\Domain\Leads\Models\Lead;
use App\Http\Middleware\InjectClientId;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateLeadCommunicationPreferences
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
        LeadAggregate::retrieve($id)->updateCommunicationPreferences($data['email'], $data['sms'], Carbon::now())->persist();

        return Lead::findOrFail($id);
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function asController(Request $request, $id)
    {
        $this->handle(
            $id,
            $request->validated(),
        );
    }
}
