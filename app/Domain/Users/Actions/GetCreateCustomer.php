<?php

declare(strict_types=1);

namespace App\Domain\Users\Actions;

use App\Domain\Agreements\AgreementCategories\Projections\AgreementCategory;
use App\Domain\Users\Models\Customer;
use App\Domain\Users\Models\User;
use App\Domain\Users\Services\Helpers\Helper;
use Inertia\Inertia;
use Inertia\Response;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class GetCreateCustomer
{
    use AsAction;

    public function handle(string $current_team_id, User $user): array
    {
        $locations_records = Helper::setUpLocationsObject($current_team_id, $user->isClientUser(), $user->client_id)->get();

        $locations = [];
        foreach ($locations_records as $location) {
            $locations[$location->gymrevenue_id] = $location->name;
        }

        return [
            'user_id' => $user->id,
            'locations' => $locations,
        ];
    }

    public function authorize(ActionRequest $request): bool
    {
        return $request->user()->can('customers.create', Customer::class);
    }

    public function asController(): array
    {
        $user = auth()->user();

        return $this->handle(
            Helper::getCurrentTeam($user->default_team_id)->id,
            $user
        );
    }

    public function htmlResponse(array $data): Response
    {
        return Inertia::render('Customers/Create', [
            'user_id' => $data['user_id'],
            'locations' => $data['locations'],
            'agreementCategories' => AgreementCategory::where('name', '!=', AgreementCategory::NAME_MEMBERSHIP)->get(),
        ]);
    }
}
