<?php

namespace App\Domain\Users\Actions;

use App\Domain\Users\Models\User;
use App\Domain\Users\UserAggregate;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateFiles extends \App\Actions\Clients\Files\CreateFiles
{
    use AsAction;
    public string $commandSignature = 'endUser:uploadProfilePicture';
    public string $commandDescription = 'Upload Profile Picture';

    public function handle($data, $current_user = null)
    {
        $files = [];

        foreach ($data as $key => $file) {
            if (array_key_exists('user_id', $file)) {
                $model = User::find($file['user_id']);
            } else {
                $model = User::find($current_user);
            }
            $files[] = UserAggregate::retrieve($model->id)->uploadFile($file)->persist();
        }

        return $files;
    }
}
