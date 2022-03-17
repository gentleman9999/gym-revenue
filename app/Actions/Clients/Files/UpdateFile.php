<?php

namespace App\Actions\Clients\Files;

use App\Aggregates\Clients\FileAggregate;
use App\Models\File;
use Illuminate\Support\Arr;
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
            'id' => 'uuid|required',
            'admin' => 'boolean|sometimes|nullable',
            'account_owner' => 'boolean|sometimes|nullable',
            'regional_admin' => 'boolean|sometimes|nullable',
            'location_manager' => 'boolean|sometimes|nullable',
            'employee' => 'boolean|sometimes|nullable',
        ];
    }

    public function handle($data, $current_user = null)
    {
        $data['permissions'] = json_encode(Arr::except($data, ['id']));
        $data = Arr::except($data, ['admin','account_owner','regional_admin','location_manager','employee']);
        FileAggregate::retrieve($data['id'])->updatePermissions($current_user->id ?? "Auto Generated", $data)->persist();
        return File::findOrFail($data['id']);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();
        return $current_user->can('files.create', $current_user->currentTeam()->first());
    }

    public function asController(ActionRequest $request)
    {

        $file = $this->handle(
            $request->validated(),
            $request->user(),
        );

        Alert::success("File permissions for '{$file->filename}' updated.")->flash();

        return Redirect::back();

    }
}
