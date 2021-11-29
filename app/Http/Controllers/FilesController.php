<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
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
            '*.url' => 'url|required',
            '*.filename' => 'max:255|required',
            '*.original_filename' => 'max:255|required',
            '*.extension' => 'required|string|min:3|max:4',
            '*.bucket' => 'max:255|required',
            '*.key' => 'max:255|required',
//            '*.is_public' =>'boolean|required',
            '*.size' => 'integer|min:1|required',//TODO: add max size
            '*.client_id' => 'exists:clients,id|required'
        ]);
        $success = File::insert($data);
//        if($success !== true){
//            TODO: flash error or something
//        }
        return Redirect::route('files');

//        dd($data);
    }

    public function delete(Request $request)
    {

    }

}
