<?php

namespace App\Domain\Files\Actions;

use App\Actions\GymRevAction;

use App\Aggregates\Clients\FileAggregate;
use App\Models\File;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Prologue\Alerts\Facades\Alert;

class UpdateFileFolder extends GymRevAction
{
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
            'folder' => 'present',
        ];
    }

    public function handle($data)
    {
        $id = $data['id'];
        FileAggregate::retrieve($id)->updateFolder($data['user_id'] ?? "Auto Generated", $data)->persist();

        return File::findOrFail($id);
    }

    public function mapArgsToHandle($args): array
    {
        return [$args];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('files.update', File::class);
    }

    public function asController(ActionRequest $request, $id)
    {
        $user = $request->user();
        $user_id = $user->id ?? null;
        $data = $request->validated();
        $data['id'] = $id;
        $data['user_id'] = $user_id;

        $file = $this->handle(
            $data
        );

        Alert::success("File added to folder.")->flash();

        return Redirect::back();
    }
}
