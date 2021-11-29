<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FilesController extends Controller
{
    public function index(Request $request)
    {
//       $files = GetFilesFromFolder::run($request->user()->currentClientId());

        $client_id = request()->user()->currentClientId();
        $is_client_user = request()->user()->isClientUser();

        $page_count = 5;

//        $files = File::with('client')
//            ->whereClientId($client_id)
//            ->filter($request->only('search', 'trashed'))
//            ->paginate($page_count);
        $files = ['data' => [['uuid' => '123kj-sdcv2-asdf-313ef', 'name' =>'test.pdf']]];

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
            '*.uuid' => 'required',
            '*.url' => 'url|required',
            '*.filename' => 'required',
            '*.original_filename' => 'required',
            '*.extension' => 'required|string|min:3|max:4',
            '*.bucket' => 'required',
            '*.key' => 'required',
            '*.is_public' =>'boolean|required',
            '*.size' => 'integer|min:1|required'//TODO: add max size
        ]);
        dd($data);
    }

    public function delete(Request $request)
    {

    }

}
