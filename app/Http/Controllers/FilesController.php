<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Prologue\Alerts\Facades\Alert;

class FilesController extends Controller
{
    public function index(Request $request)
    {
//       $files = GetFilesFromFolder::run($request->user()->currentClientId());

        $client_id = request()->user()->currentClientId();
        $is_client_user = request()->user()->isClientUser();

        $page_count = 10;

        $files = File::with('client')
            ->whereClientId($client_id)
            ->whereUserId(null)
            ->filter($request->only('search', 'trashed'))
            ->paginate($page_count);

        return Inertia::render('Files/Show', [
            'files' => $files
        ]);
    }

    public function edit($id)
    {
        if (!$id) {
            Alert::error("No file ID provided")->flash();
            return Redirect::route('files');
        }

        return Inertia::render('Files/Edit', [
            'file' => File::find($id),
        ]);
    }

    public function upload(Request $request)
    {
        return Inertia::render('Files/Upload', [
        ]);
    }

    public function update(Request $request, $id)
    {
        if (!$id) {
            Alert::error("No file ID provided")->flash();
            return Redirect::route('files');
        }
        $data = $request->validate([
            'filename' => 'max:255|required',
            'file_uuid' => 'uuid'
        ]);

        $file = File::findOrFail($id);
        $new_filename = $data['filename'];
        $old_filename= $file->filename;
        $data['extension'] = last(explode('.', $new_filename));

        if($new_filename !== $old_filename){
            $file->updateOrFail($data);
        }
        Alert::success("File '{$file->filename}' updated")->flash();

        return Redirect::route('files');
    }
}
