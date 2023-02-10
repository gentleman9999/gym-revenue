<?php

declare(strict_types=1);

namespace App\Domain\Files\Actions;

use App\Actions\GymRevAction;
use App\Domain\Clients\Projections\Client;
use App\Domain\Users\Models\User;
use App\Models\File;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Prologue\Alerts\Facades\Alert;

class CreateFiles extends GymRevAction
{
    /**
     * @return array<string>
     */
    public function rules(): array
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

    /**
     * @param array<string, mixed> $args
     *
     * @return array<string, mixed>
     */
    public function mapArgsToHandle(array $args): array
    {
        return $args;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<File>
     */
    public function handle(array $data, ?User $current_user = null): array
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

    public function authorize(ActionRequest $request): bool
    {
        return $request->user()->can('files.create', File::class);
    }

    /**
     *
     * @return File[]
     */
    public function asController(ActionRequest $request): array
    {
        return $this->handle($request->validated(), $request->user());
    }

    /**
     * @param File[] $data
     *
     */
    public function htmlResponse(array $data = []): RedirectResponse
    {
        $fileCount = count($data);
        Alert::success("{$fileCount} Files created")->flash();

        return Redirect::back();
    }
}
