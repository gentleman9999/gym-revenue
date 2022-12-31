<?php

declare(strict_types=1);

namespace App\Domain\Users\Actions;

use App\Domain\Users\Models\Customer;
use App\Domain\Users\Services\Helpers\Helper;
use Illuminate\Database\Eloquent\Collection;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class ExportCustomer
{
    use AsAction;

    public function handle(string $current_team_id, string $client_id): Collection
    {
        $customers = [];

        $customers_model = Helper::setUpCustomersObject($current_team_id, $client_id);

        if (! empty($customers_model)) {
            $customers = $customers_model
                ->with('location')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return $customers;
    }

    public function authorize(ActionRequest $request): bool
    {
        return $request->user()->can('customers.read', Customer::class);
    }

    public function asController(): Collection
    {
        $user = request()->user();

        return $this->handle(
            Helper::getCurrentTeam($user->default_team_id)->id,
            $user->client_id
        );
    }
}
