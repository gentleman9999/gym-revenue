<?php

declare(strict_types=1);

namespace App\Domain\EndUsers\Customers\Actions;

use App\Domain\EndUsers\Customers\Projections\Customer;
use App\Domain\EndUsers\EndUserAggregate;
use App\Domain\EndUsers\Projections\EndUser;
use App\Domain\Locations\Projections\Location;
use App\Models\Note;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class ViewCustomer
{
    use AsAction;

    public function handle(EndUser $end_user, int $user_id): array
    {
        $gr_location_id = Customer::whereId($end_user->id)->pluck('gr_location_id')[0];
        $locid = Location::where('gymrevenue_id', $gr_location_id)->first();
        $preview_note = Note::select('note')->whereEntityId($end_user->id)->get();
        $aggy = EndUserAggregate::retrieve($end_user->id);

        return [
            'customer' => $end_user,
            'user_id' => $user_id,
            'club_location' => $locid,
            'interactionCount' => $aggy->getInteractionCount(),
            'preview_note' => $preview_note,
        ];
    }

    public function authorize(ActionRequest $request): bool
    {
        return $request->user()->can('customers.read', Customer::class);
    }

    public function asController(EndUser $end_user): array
    {
        return $this->handle($end_user, request()->user()->id);
    }
}
