<?php

declare(strict_types=1);

namespace App\Domain\Users\Actions;

use App\Domain\Teams\Models\Team;
use App\Domain\Users\Models\Customer;
use App\Domain\Users\Models\EndUser;
use App\Domain\Users\Models\User;
use App\Domain\Users\Services\Helpers\Helper;
use App\Models\ReadReceipt;
use Inertia\Inertia;
use Inertia\Response;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class EditCustomer
{
    use AsAction;

    public function handle(EndUser $end_user, User $user, Team $current_team): array
    {
        $locations_records = Helper::setUpLocationsObject($current_team->id, $user->isClientUser(), $user->client_id)->get();

        $locations = [];
        foreach ($locations_records as $location) {
            $locations[$location->gymrevenue_id] = $location->name;
        }

        $team_users = $current_team->team_users()->get();

        $end_user->load('notes');

        //for some reason inertiajs converts "notes" key to empty string.
        //so we set all_notes
        $customer_data = $end_user->toArray();
        $customer_data['all_notes'] = $end_user->notes->toArray();

        foreach ($customer_data['all_notes'] as $key => $value) {
            if (ReadReceipt::whereNoteId($customer_data['all_notes'][$key]['id'])->first()) {
                $customer_data['all_notes'][$key]['read'] = true;
            } else {
                $customer_data['all_notes'][$key]['read'] = false;
            }
        }

        return [
            'customer_data' => $customer_data,
            'user_id' => $user->id,
            'locations' => $locations,
        ];
    }

    public function authorize(ActionRequest $request): bool
    {
        return $request->user()->can('customers.update', Customer::class);
    }

    public function asController(EndUser $end_user): array
    {
        $user = request()->user();

        return $this->handle(
            $end_user,
            $user,
            Helper::getCurrentTeam($user->default_team_id)
        );
    }

    public function htmlResponse(array $data): Response
    {
        return Inertia::render('Customers/Edit', [
            'customer' => $data['customer_data'],
            'user_id' => $data['user_id'],
            'locations' => $data['locations'],
        ]);
    }
}
