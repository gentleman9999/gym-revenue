<?php

namespace App\Domain\Files\Actions;

use App\Domain\Clients\Projections\Client;
use App\Domain\Users\Models\User;
use App\Http\Middleware\InjectClientId;
use App\Models\File;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class CreateFiles
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
            '*.id' => 'uuid|required',
            '*.filename' => 'max:255|required',
            '*.original_filename' => 'max:255|required',
            '*.extension' => 'required|string|min:3|max:4',
            '*.bucket' => 'max:255|required',
            '*.key' => 'max:255|required',
//            '*.is_public' =>'boolean|required',
            '*.size' => 'integer|min:1|required',//TODO: add max size
            '*.client_id' => 'exists:clients,id|required',
            '*.entity_id' => 'sometimes',
            '*.user_id' => 'nullable|exists:users,id',
            '*.visibility' => 'sometimes',
        ];
    }

    public function handle($data, $current_user = null)
    {
        $files = [];

        foreach ($data as $file) {
            if (array_key_exists('user_id', $file)) {
                $model = User::find($file['user_id']);
            } else {
                // No User ID means they should be coming from File Manager and will be Client scoped
                $model = Client::find($file['client_id']);
            }
            $files[] = CreateFile::run($file, $model, $current_user);
        }

        return $files;
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('files.create', File::class);
    }

    public function asController(ActionRequest $request)
    {
        $files = $this->handle(
            $request->validated(),
            (! is_null($request->user()) ? $request->user() : request()->user()),
        );

        $fileCount = count($files);
        Alert::success("{$fileCount} Files created")->flash();

//        return Redirect::route('files');
        return Redirect::back();
    }
}
