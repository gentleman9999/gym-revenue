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

    }

    public function delete(Request $request)
    {

    }

}
