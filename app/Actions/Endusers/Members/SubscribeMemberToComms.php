<?php

namespace App\Actions\Endusers\Members;

use App\Aggregates\Endusers\MemberAggregate;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class SubscribeMemberToComms
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
            //no rules since we only accept an id route param, which is validated in the route definition
        ];
    }

    public function handle($id)
    {
        MemberAggregate::retrieve($id)->subscribeToComms(Carbon::now())->persist();
    }

//    public function authorize(ActionRequest $request): bool
//    {
//        $current_user = $request->user();
//
//        return $current_user->can('leads.delete', Lead::class);
//    }

    public function asController(Request $request, $id)
    {
        $this->handle(
            $id,
        );
//        return Redirect::back();
    }
}
