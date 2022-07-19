<?php

namespace App\Domain\Clients\Actions;

use App\Domain\Teams\Models\Team;
use App\Enums\SecurityGroupEnum;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class GetTeams
{
    use AsAction;

    public function rules(): array
    {
        return  ['client_id' => 'nullable'];
    }

    public function handle(string $client_id = null)
    {
        return Team::withoutGlobalScopes()->whereClientId($client_id)->orderBy('home_team', 'desc')->orderBy('name', 'asc')->get(['name', 'id']);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->inSecurityGroup(SecurityGroupEnum::ADMIN);
    }

    public function asController(ActionRequest $request)
    {
        $data = $request->validated();

        return $this->handle($data['client_id'] ?? null);
    }
}
