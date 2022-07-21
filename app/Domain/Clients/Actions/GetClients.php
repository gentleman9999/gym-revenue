<?php

namespace App\Domain\Clients\Actions;

use App\Domain\Clients\Models\Client;
use App\Enums\SecurityGroupEnum;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class GetClients
{
    use AsAction;

    public function handle()
    {
        return Client::orderBy('name', 'asc')->get(['name', 'id']);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->inSecurityGroup(SecurityGroupEnum::ADMIN);
    }
}
