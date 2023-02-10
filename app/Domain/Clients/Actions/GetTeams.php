<?php

declare(strict_types=1);

namespace App\Domain\Clients\Actions;

use App\Domain\Teams\Models\Team;
use App\Enums\SecurityGroupEnum;
use Illuminate\Database\Eloquent\Collection;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class GetTeams
{
    use AsAction;

    public function rules(): array
    {
        return ['client_id' => 'nullable'];
    }

    public function handle(?string $client_id = null, $current_user = null): Collection
    {
        if ($current_user && $current_user->inSecurityGroup(SecurityGroupEnum::ADMIN)) {
            $Team = Team::withoutGlobalScopes()->whereClientId($client_id);
        } elseif ($current_user) {
            $Team = Team::whereIn('id', $current_user->allTeams()->pluck('id'));
        }

        return $Team->orderBy('home_team', 'desc')->orderBy('name', 'asc')->get(['name', 'id']);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return ! $current_user->inSecurityGroup(SecurityGroupEnum::EMPLOYEE);
    }

    public function asController(ActionRequest $request): Collection
    {
        $data = $request->validated();

        return $this->handle($data['client_id'] ?? null, $request->user());
    }
}
