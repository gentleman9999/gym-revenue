<?php

namespace App\Actions\Clients\Files;

use App\Aggregates\Clients\FileAggregate;
use App\Models\File;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Prologue\Alerts\Facades\Alert;
use Lorisleiva\Actions\Concerns\AsAction;


class UpdateFile
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
            //TODO: why doesn't the line below this work? says permissions must be an array (which it is)
//            'permissions' => 'required|array:admin,account_owner,regional_admin,location_manager,employee',
            'permissions' => 'present',
        ];
    }

    public function handle($id, $data, $current_user = null)
    {
        FileAggregate::retrieve($id)->updatePermissions($current_user->id ?? "Auto Generated", $data)->persist();
        return File::findOrFail($id);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();
        return $current_user->can('files.update', $current_user->currentTeam()->first());
    }

    public function asController(ActionRequest $request, $id)
    {

        $file = $this->handle(
            $id,
            $request->validated(),
            $request->user(),
        );

        Alert::success("File permissions for '{$file->filename}' updated.")->flash();

        return Redirect::back();

    }
}
