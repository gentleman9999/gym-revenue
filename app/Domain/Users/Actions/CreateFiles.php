<?php

namespace App\Domain\Users\Actions;

use App\Domain\Users\Models\User;
use App\Domain\Users\UserAggregate;

class CreateFiles extends \App\Domain\Files\Actions\CreateFiles
{
    public function handle($data, $current_user = null): array
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
