<?php

declare(strict_types=1);

namespace App\Domain\Users\Actions;

use App\Domain\Locations\Projections\Location;
use App\Domain\Teams\Models\Team;
use App\Domain\Users\Models\Customer;
use App\Domain\Users\Models\User;
use App\Domain\Users\Services\Helpers\Helper;
use App\Enums\LiveReportingEnum;
use App\Models\LiveReportsByDay;
use Inertia\Inertia;
use Inertia\Response;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class GetCustomers
{
    use AsAction;

    public function handle(Team $current_team, User $user, array $filter_params, string $sort = null, string $dir = null): array
    {
        $page_count = 10;
        $customers = [];

        /**
         * BUSINESS RULES
         * 1. There must be an active client and an active team.
         * 2. Client Default Team, then all customers from the client
         * 3. Else, get the team_locations for the active_team
         * 4. Query for client id and locations in
         */

        $client_id = $user->client_id;

        $customers_model = Helper::setUpCustomersObject($current_team->id, $client_id);

        if (! empty($customers_model)) {
            $customers = $customers_model
                ->with('location')
                ->with('notes')
                ->filter($filter_params)
                ->orderBy('created_at', 'desc')
                ->sort()
                ->paginate($page_count)
                ->appends(request()->except('page'));

            //THIS DOESN'T WORK BECAUSE OF PAGINATION BUT IT MAKES IT LOOK LIKE IT'S WORKING FOR NOW
            //MUST FIX BY DEMO 6/15/22
            //THIS BLOCK HAS TO BE REMOVED & QUERIES REWRITTEN WITH JOINS SO ACTUAL SORTING WORKS WITH PAGINATION
            if ($sort != '') {
                if ($dir == 'DESC') {
                    $sorted_result = $customers->getCollection()->sortByDesc($sort)->values();
                } else {
                    $sorted_result = $customers->getCollection()->sortBy($sort)->values();
                }
                $customers->setCollection($sorted_result);
            }
        }

        $new_customer_count = 0;
        if ($user->current_location_id) {
            $new_customer_count = LiveReportsByDay::whereEntity('customer')
                ->where('date', '=', date('Y-m-d'))
                ->whereAction(LiveReportingEnum::ADDED)
                ->whereGrLocationId(Location::find($user->current_location_id)->gymrevenue_id)->first();
            if ($new_customer_count) {
                $new_customer_count = $new_customer_count->value;
            }
        }

        $available_customer_owners = [];

        foreach ($current_team->team_users()->get() as $team_user) {
            $available_customer_owners[$team_user->user_id] = "{$team_user->user->name}";
        }

        return [
            'customers' => $customers,
            'available_customer_owners' => $available_customer_owners,
            'locations' => Helper::getLocations($current_team->id, $user->isClientUser(), $client_id),
            'new_customer_count' => $new_customer_count,
        ];
    }

    public function authorize(ActionRequest $request): bool
    {
        return $request->user()->can('customers.read', Customer::class);
    }

    public function asController(ActionRequest $request): array
    {
        $user = request()->user();

        return $this->handle(
            Helper::getCurrentTeam($user->default_team_id),
            $user,
            $request->only(
                'search',
                'trashed',
                'createdat',
                'grlocation',
                'date_of_birth',
                'nameSearch',
                'phoneSearch',
                'emailSearch',
                'agreementSearch',
                'lastupdated'
            ),
            $request->sort,
            $request->dir
        );
    }

    public function htmlResponse(array $data, ActionRequest $request): Response
    {
        return Inertia::render('Customers/Index', [
            'customers' => $data['customers'],
            'routeName' => request()->route()->getName(),
            'title' => 'Customers',
            'owners' => $data['available_customer_owners'],
            'locations' => $data['locations'],
            'filters' => $request->all(
                'search',
                'trashed',
                'createdat',
                'grlocation',
                'claimed',
                'date_of_birth',
                'nameSearch',
                'phoneSearch',
                'emailSearch',
                'agreementSearch',
                'lastupdated'
            ),
            'grlocations' => Location::all(),
            'newCustomerCount' => $data['new_customer_count'],
        ]);
    }
}
