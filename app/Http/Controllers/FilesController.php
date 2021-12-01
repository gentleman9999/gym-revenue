<?php

namespace App\Http\Controllers;

use App\Aggregates\Clients\FileAggregate;
use App\Models\Clients\Location;
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


            FileAggregate::retrieve($file->id)
                ->renameFile($request->user()->id, $old_filename, $new_filename)
                ->persist();
        }
        Alert::success("File '{$file->filename}' updated")->flash();

        return Redirect::route('files');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            '*.id' => 'uuid|required',
            '*.filename' => 'max:255|required',
            '*.original_filename' => 'max:255|required',
            '*.extension' => 'required|string|min:3|max:4',
            '*.bucket' => 'max:255|required',
            '*.key' => 'max:255|required',
//            '*.is_public' =>'boolean|required',
            '*.size' => 'integer|min:1|required',//TODO: add max size
            '*.client_id' => 'exists:clients,id|required'
        ]);
        foreach($data as $row){
//
            $file = File::create($row);
            $file->url = Storage::disk('s3')->url($file->key);
            $success = $file->save();
            if($success){
                Alert::success("File '{$file->filename}' created")->flash();
            }else{
                Alert::error("An error occurred while creating'{$file->filename}' ")->flash();
            }

            FileAggregate::retrieve($file->id)
                ->createFile($request->user()->id, $file->key, $file->client_id)
                ->persist();
        }
        return Redirect::route('files');
    }

    public function trash(Request $request, $id)
    {
        if (!$id) {
            Alert::error("No file ID provided")->flash();
            return Redirect::back();
        }

        $file = File::findOrFail($id);
        $file->deleteOrFail();
        Alert::success("File '{$file->filename}' trashed")->flash();


        FileAggregate::retrieve($file->id)
            ->trashFile($request->user()->id)
            ->persist();

        return Redirect::route('files');
    }

    public function delete(Request $request, $id)
    {
        if (!$id) {
            Alert::error("No file ID provided")->flash();
            return Redirect::back();
        }

        $file = File::withTrashed()->findOrFail($id);
        $file->forceDelete();

        Alert::success("File '{$file->filename}' permanently deleted")->flash();

        FileAggregate::retrieve($file->id)
            ->deleteFile($request->user()->id, $file->key)
            ->persist();


        return Redirect::route('files');
    }

    public function restore(Request $request, $id)
    {
        if (!$id) {
            Alert::error("No file ID provided")->flash();
            return Redirect::route('files');
        }
        $file = File::withTrashed()->findOrFail($id);
        $file->restore();
        Alert::success("File '{$file->filename}' restored")->flash();

        FileAggregate::retrieve($file->id)
            ->restoreFile($request->user()->id)
            ->persist();

        return Redirect::back();
    }

}
