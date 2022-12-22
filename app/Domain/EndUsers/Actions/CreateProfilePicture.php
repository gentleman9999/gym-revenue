<?php

declare(strict_types=1);

namespace App\Domain\EndUsers\Actions;

use App\Domain\EndUsers\EndUserAggregate;
use App\Domain\EndUsers\Projections\EndUser;
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
            EndUserAggregate::retrieve($model->id)->update(['profile_picture_file_id' => $file['id']])->persist();
            $files[] = EndUserAggregate::retrieve($model->id)->uploadFile($file)->persist();
        }

        return $files;
    }
}
