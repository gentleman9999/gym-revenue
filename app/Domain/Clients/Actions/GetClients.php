<?php

declare(strict_types=1);

namespace App\Domain\Clients\Actions;

use App\Domain\Clients\Projections\Client;
use App\Enums\SecurityGroupEnum;
use Illuminate\Database\Eloquent\Collection;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class GetClients
{
    use AsAction;

    public function handle(): Collection
    {
        return Client::orderBy('name', 'asc')->get(['name', 'id']);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->inSecurityGroup(SecurityGroupEnum::ADMIN);
    }
}
