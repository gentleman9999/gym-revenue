<?php

namespace App\GraphQL\Queries;

use App\Models\File;
use App\Models\Folder;

final class FolderContent
{
    public function __invoke($_, array $args)
    {
        $client_id = request()->user()->client_id;
        $folder = Folder::find($args["id"]);
        $folder_id = $folder ? $folder->id : null;
        $folder_name = $folder ? $folder->name : null;

        $files = File::with('client')
            ->whereClientId($client_id)
            ->whereHidden(false)
            ->whereEntityType(null);
        if ($folder || ! $args['filter']['search']) {
            $files = $files->whereFolder($folder_id);
        }
        $files = $files->filter($args['filter'])->sort()->get();

        $folders = [];
        if (! $folder) {
            $folders = Folder::whereClientId($client_id)
            ->withCount('files')
            ->filter($args['filter'])
                ->get();
        }

        return [
            'files' => $files,
            'folders' => $folders,
            'name' => $folder_name,
        ];
    }
}
