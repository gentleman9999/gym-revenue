<?php

namespace App\Http\Controllers;

use App\Aggregates\Clients\FileAggregate;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class FilesController extends Controller
{
    public function index(Request $request)
    {
//       $files = GetFilesFromFolder::run($request->user()->currentClientId());

        $client_id = request()->user()->currentClientId();
        $is_client_user = request()->user()->isClientUser();

        $page_count = 5;

        $files = File::with('client')
            ->whereClientId($client_id)
            ->filter($request->only('search', 'trashed'))
            ->paginate($page_count);

        return Inertia::render('Files/Show', [
            'files' => $files
        ]);
    }

    public function upload(Request $request)
    {
        return Inertia::render('Files/Upload', [
        ]);
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
            $file->save();

            FileAggregate::retrieve($file->id)
                ->createFile($request->user()->id, $file->key, $file->client_id)
                ->persist();
        }
//        $success = File::insert($data);
//        if($success !== true){
//            TODO: flash error or something
//        }
        return Redirect::route('files');

//        dd($data);
    }

    public function trash(Request $request, $id)
    {
        if (!$id) {
            //TODO:flash error
            return Redirect::back();
        }

        $file = File::findOrFail($id);
        $file->deleteOrFail();

        FileAggregate::retrieve($file->id)
            ->trashFile($request->user()->id)
            ->persist();

        return Redirect::route('files');
    }

    public function delete(Request $request, $id)
    {
        if (!$id) {
            //TODO:flash error
            return Redirect::back();
        }

        $file = File::findOrFail($id);
        $file->forceDelete();

        FileAggregate::retrieve($file->id)
            ->deleteFile($request->user()->id, $file->key)
            ->persist();

        return Redirect::route('files');
    }

    public function restore(Request $request, $id)
    {
        if (!$id) {
            //TODO:flash error
            return Redirect::route('files');
        }
        $file = File::withTrashed()->findOrFail($id);
        $file->restore();
        FileAggregate::retrieve($file->id)
            ->restoreFile($request->user()->id)
            ->persist();

        return Redirect::back();
    }

}
