<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class FoldersController extends Controller
{
    public function index(Request $request)
    {
        $client_id = request()->user()->currentClientId();

        if ($client_id === null) {
            return Redirect::route('dashboard');
        }

        $page_count = 10;

        $folders = Folder::whereClientId($client_id)
                ->with('files')
                ->filter($request->only('search', 'trashed'))
                ->sort()
                ->paginate($page_count);


        return Inertia::render('Folders/Show', [
            'folders' => $folders,
        ]);
    }

    public function viewFiles(Folder $folder)
    {
        $client_id = request()->user()->client_id;

        if ($client_id === null) {
            return Redirect::route('dashboard');
        }

        $files = File::whereFolder($folder->id)
            ->get();

        return Inertia::render('Files/Show', [
            'files' => ['data' => $files],
            'folderName' => $folder->name,
        ]);
    }
}
