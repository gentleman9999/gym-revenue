<?php

namespace App\Domain\Leads\Actions;

use App\Domain\Leads\Models\Lead;
use App\Http\Middleware\InjectClientId;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use function request;

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

    public function handle(array $data)
    {
        if (array_key_exists('per_page', $data)) {
            $page_count = $data['per_page'] > 0 && $data['per_page'] < 1000 ? $data['per_page'] : 10;
        } else {
            $page_count = 10;
        }

        return Lead::whereClientId($data['client_id'])
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
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function asController(ActionRequest $request)
    {
        $leads = $this->handle(
            $request->validated(),
        );

        return $leads;
    }
}
