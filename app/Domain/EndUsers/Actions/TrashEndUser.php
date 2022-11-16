<?php

namespace App\Domain\EndUsers\Actions;

use App\Domain\EndUsers\EndUserAggregate;
use App\Domain\EndUsers\Projections\EndUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Prologue\Alerts\Facades\Alert;

class TrashEndUser extends BaseEndUserAction
{
    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'reason' => ['required','string'],
        ];
    }

    public function handle(EndUser $endUser, $reason)
    {
        EndUserAggregate::retrieve($endUser->id)->trash($reason)->persist();

        return $endUser;
    }

    public function asController(ActionRequest $request, EndUser $endUser)
    {
        if (is_null($endUser->id)) {
            $endUser->id = basename(request()->getUri());
        }

        return $this->handle(
            $endUser,
            $request->validated()['reason']
        );
    }

    public function htmlResponse(EndUser $endUser): RedirectResponse
    {
        Alert::success("End User '{$endUser->name}' was deleted")->flash();

        return Redirect::back();
    }
}
