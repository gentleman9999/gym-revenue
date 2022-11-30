<?php

namespace App\Domain\EndUsers\Actions;

use App\Domain\EndUsers\EndUserAggregate;
use App\Domain\EndUsers\Projections\EndUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Prologue\Alerts\Facades\Alert;

class DeleteEndUser extends BaseEndUserAction
{
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

    public function handle(EndUser $endUser)
    {
        EndUserAggregate::retrieve($endUser->id)->delete()->persist();

        return $endUser;
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('endusers.delete', EndUser::class);
    }

    public function asController(ActionRequest $request, EndUser $endUser): EndUser
    {
        return $this->handle(
            $endUser,
        );
    }

    public function htmlResponse(EndUser $endUser): RedirectResponse
    {
        Alert::success("End User '{$endUser->first_name}' was deleted")->flash();

        return Redirect::back();
    }
}
