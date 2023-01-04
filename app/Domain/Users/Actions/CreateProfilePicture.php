<?php

declare(strict_types=1);

namespace App\Domain\Users\Actions;

use App\Domain\Users\Aggregates\UserAggregate;
use App\Domain\Users\Models\EndUser;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateProfilePicture extends \App\Actions\Clients\Files\CreateFiles
{
    use AsAction;

    public string $commandSignature = 'endUser:uploadProfilePicture';
    public string $commandDescription = 'Upload Profile Picture';

    public function handle($data, $current_user = null)
    {
        $files = [];

        foreach ($data as $key => $file) {
            $model = EndUser::find($file['entity_id']);
            $file['uuid'] = $file['id'];
            UserAggregate::retrieve($model->id)->update(['profile_picture_file_id' => $file['id']])->persist();
            UpdateUser::run($model->id, [
                'profile_photo_path' => "https://{$file['bucket']}.s3.amazonaws.com/{$file['key']}",
            ]);
            $files[] = UserAggregate::retrieve($model->id)->uploadFile($file)->persist();
        }

        return $files;
    }
}
